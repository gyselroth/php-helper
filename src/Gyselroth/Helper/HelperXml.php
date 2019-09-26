<?php

/**
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Gyselroth\Helper;

use Gyselroth\Helper\Exception\XmlException;
use Gyselroth\Helper\Interfaces\ConstantsXmlInterface;

class HelperXml implements ConstantsXmlInterface
{
    public const LOG_CATEGORY = 'xmlhelper';

    /**
     * Simple XML validation
     *
     * @param  string $str
     * @return bool         Does given string contain valid XML?
     */
    public static function isValidXml(string $str): bool
    {
        return (bool)\simplexml_load_string(
            $str, 'SimpleXmlElement',
            LIBXML_NOERROR + LIBXML_ERR_FATAL + LIBXML_ERR_NONE);
    }

    /**
     * @param  string $xml
     * @param  array  $levels          If empty: all levels
     * @param  array  $excludeTagNames Tag names not to be counted
     * @param  array  $excludeTagTypes Tag types not to be counted
     * @return array                        Nodes on given/all levels, not counting elements matching given filters
     * @throws \Exception
     */
    public static function getNodes(
        string $xml,
        array $levels,
        array $excludeTagNames = [],
        array $excludeTagTypes = []
    ): array
    {
        $tags = self::getTagsFromXml($xml);

        return
            [] === $excludeTagNames
            && [] === $excludeTagTypes
            && [] === $levels
                ? $tags
                : self::getItemsInArrayOfNodes($levels, $excludeTagNames, $excludeTagTypes, $tags);
    }

    /**
     * @param  array $levels            Levels of nodes to be counted
     * @param  array $excludeTagNames   Tag names not to be counted
     * @param  array $excludeTagTypes   Tag types not to be counted
     * @param  array $nodes             Array of XML nodes (tags), each having attributes: 'tag', 'type', 'level' (, optionally: 'attributes')
     * @return array
     */
    public static function getItemsInArrayOfNodes(
        array $levels,
        array $excludeTagNames,
        array $excludeTagTypes,
        $nodes
    ): array
    {
        $nodesFiltered = [];
        $excludeTagNames = \array_map('strtoupper', $excludeTagNames);
        foreach ($nodes as $node) {
            if (!\in_array($node['tag'], $excludeTagNames, true)
                && !\in_array($node['type'], $excludeTagTypes, true)
                && (
                    [] === $levels
                    || \in_array($node['level'], $levels, true)
                )
            ) {
                $nodesFiltered[] = $node;
            }
        }

        return $nodesFiltered;
    }

    /**
     * @param  string $xml
     * @param  array  $levels          If empty: all levels
     * @param  array  $excludeTagNames Tag names not to be counted
     * @param  array  $excludeTagTypes Tag types not to be counted
     * @return int                          Amount of nodes on given/all levels, not counting elements matching given filters
     * @throws \Exception
     */
    public static function getAmountNodes(
        string $xml,
        array $levels,
        array $excludeTagNames = [],
        array $excludeTagTypes = []
    ):int
    {
        return \count(
            self::getNodes($xml, $levels, $excludeTagNames, $excludeTagTypes));
    }

    /**
     * @param  array $levels            Levels of nodes to be counted
     * @param  array $excludeTagNames   Tag names not to be counted
     * @param  array $excludeTagTypes   Tag types not to be counted
     * @param  array $nodes             Array of XML nodes (tags), each having attributes: 'tag', 'type', 'level' (, optionally: 'attributes')
     * @return int
     */
    public static function countItemsInArrayOfNodes(
        array $levels,
        array $excludeTagNames,
        array $excludeTagTypes,
        $nodes
    ): int
    {
        return \count(
            self::getItemsInArrayOfNodes($levels, $excludeTagNames, $excludeTagTypes, $nodes));
    }

    /**
     * @param  \LibXMLError $error
     * @param  bool         $includeFatal
     * @param  bool         $includeErrors
     * @param  bool         $includeWarnings
     * @return string Error message
     * @throws \Exception
     */
    public static function getLibxmlErrorMessage(
        $error,
        bool $includeFatal = true,
        bool $includeErrors = false,
        bool $includeWarnings = false
    ): string
    {
        $renderErrorMessage = function($error, $message) {
            return "<strong><br/>\n"
                . $message
                . \trim($error->message) . ' on line <strong>' . $error->line
                . "</strong>\n</strong>: ";
        };

        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                if ($includeWarnings) {
                    return $renderErrorMessage($error, "Warning {$error->code}");
                }
                break;
            case LIBXML_ERR_ERROR:
                if ($includeErrors) {
                    return $renderErrorMessage($error, "Error {$error->code}");
                }
                break;
            case LIBXML_ERR_FATAL:
                if ($includeFatal) {
                    return $renderErrorMessage($error, "Fatal Error {$error->code}");
                }
                break;
            default:
                LoggerWrapper::warning(
                    "Detected unhandled error-level: {$error->level}",
                    [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY, LoggerWrapper::OPT_PARAMS => $error->level]);
        }

        return '';
    }

    /**
     * Validate given XML against given XSD, return DOMDocument or false on failure
     *
     * @param  string $pathXml
     * @param  string $pathXsd
     * @param  string $xmlVersion
     * @param  string $xmlEncoding
     * @return \DOMDocument|bool
     * @throws XmlException
     */
    public static function validate(
        string $pathXml,
        string $pathXsd,
        string $xmlVersion = '1.0',
        string $xmlEncoding = 'UTF-8'
    )
    {
        if (!\file_exists($pathXml)) {
            throw new XmlException('Failed loading XML file: ' . $pathXml);
        }
        if (!\file_exists($pathXsd)) {
            throw new XmlException('Failed loading XSD file: ' . $pathXsd);
        }

        $xml = new \DOMDocument($xmlVersion, $xmlEncoding);
        $xml->load($pathXml);

        try {
            return $xml->schemaValidate($pathXsd) ? $xml : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Print out given DOMDocument nodes
     *
     * @param  \DOMDocument|\DOMNodeList|\DomElement|array $dom
     * @param  string                                      $key
     */
    public static function debugPrint($dom, string $key = ''): void
    {
        if (self::DOM_CLASS_ELEMENT === \get_class($dom)) {
            $tempDom = new \DOMDocument();
            $node    = $tempDom->importNode($dom, true);
            $tempDom->appendChild($node);

            echo $tempDom->saveXML();
        } elseif (self::DOM_CLASS_NODE_LIST === \get_class($dom)) {
            // Print-out nodeList XML
            $tempDom = new \DOMDocument();
            foreach ($dom as $node) {
                $tempDom->appendChild($tempDom->importNode($node, true));
            }

            echo $tempDom->saveXML();
        } elseif (\is_array($dom)) {
            // Print-out all DOMDocuments in array
            foreach ($dom as $domKey => $domDocumentItem) {
                self::debugPrint($domDocumentItem, $domKey);
            }
        } else {
            // Print-out DOMDocument
            if (!empty($key)) {
                echo "\n\n" . str_repeat('.', 80) . "\n\n$key\n" . str_repeat('-', \strlen($key)) . "\n\n";
            }
            echo $dom->saveXML();
        }
    }

    /**
     * Perform string replacements over all node values in given DOMDocument
     *
     * @param  array               $search  Strings to be replaced
     * @param  array               $replace Values to replace the original string
     * @param  \DOMDocument|\DomNode $dom     "haystack": DOMDocument to perform the replacements upon
     * @return \DOMDOcument
     */
    public static function strReplaceNodeValues(array $search, array $replace, $dom): \DOMDocument
    {
        $xml = \str_replace($search, $replace, $dom->saveXML());

        $xmlVersion  = $dom->version;
        $xmlEncoding = $dom->encoding;

        $dom = new \DOMDocument($xmlVersion, $xmlEncoding);
        $dom->loadXML($xml);

        return $dom;
    }

    /**
     * @param  string $xml
     * @return array  Array containing all tags of given XML, categorized into types: 'open' / 'close' / 'complete'
     * @throws \Exception
     */
    public static function getTagsFromXml(string $xml): array
    {
        if (\function_exists('mb_convert_encoding')) {
            $xml = \mb_convert_encoding(
                $xml,
                self::ENCODING_UTF_8,
                \mb_detect_encoding($xml));
        } else {
            LoggerWrapper::warning('HelperXml::getTagsFromXml() recommends installation of PHP extension: mbstring. Skipping encoding conversion for now.');
        }

        $parser = \xml_parser_create(self::ENCODING_UTF_8);
        $data   = [];
        \xml_parse_into_struct($parser, $xml, $data);

        return $data;
    }

    public static function formatXmlString(string $xml): string
    {
        $dom                     = new \DOMDocument(self::VERSION_1_0);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput       = true;
        $dom->loadXML($xml);

        return $dom->saveXML();
    }

    /**
     * Convert given XML node to PHP array
     *
     * @param  \SimpleXMLElement|countable $node
     * @return array
     */
    public static function xmlNodeToArray($node): array
    {
        $nodeValues = [];

        // Loop over all child elements
        foreach ($node->children() as $key => $child) {
            if (0 === \count($child->children())) {
                // If this is the only value for this key
                if (1 === $node->$key->count()) {
                    $nodeValues[$key] = (string)$child;
                } else {
                    // If there are multiple values for the same key e.g an array
                    $nodeValues[$key][] = (string)$child;
                }
            } elseif (static::hasChildrenWithIdenticalName($node)) {
                $nodeValues[] = static::xmlNodeToArray($child);
            } else {
                $nodeValues[$key] = static::xmlNodeToArray($child);
            }
        }

        return $nodeValues;
    }

    /**
     * @param  \SimpleXMLElement|countable $node
     * @return bool
     */
    public static function hasChildrenWithIdenticalName($node): bool
    {
        if (1 === \count($node->children())) {
            return false;
        }

        $previousKey = false;
        foreach ($node->children() as $key => $_) {
            if ($previousKey !== $key
                && false !== $previousKey
            ) {
                return false;
            }
            $previousKey = $key;
        }

        return true;
    }

    /**
     * Convert array to XML-element
     *
     * @param  array            $data
     * @param  \SimpleXMLElement $xml
     * @return \SimpleXMLElement
     */
    public static function arrayToXml(array $data, $xml): \SimpleXMLElement
    {
        foreach ($data as $key => $value) {
            if (\is_array($value)) {
                self::arrayToXml($value, $xml->addChild($key));
            } else {
                $xml->addChild($key, \htmlspecialchars($value));
            }
        }

        return $xml;
    }
}
