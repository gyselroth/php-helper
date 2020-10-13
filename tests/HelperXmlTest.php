<?php

/**
 * Copyright (c) 2017-2020 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Tests;

use Gyselroth\Helper\HelperXml;

class HelperXmlTest extends HelperTestCase
{
    public function testIsValidXml(): void
    {
        self::assertFalse(HelperXml::isValidXml('<xml/>'));
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function testGetNodes(): void
    {
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/mock.xml');

        self::assertCount(6, HelperXml::getNodes($xml, [1]));

        self::assertCount(2, HelperXml::getNodes($xml, [1], [], ['cdata']));

        self::assertCount(4, HelperXml::getNodes($xml, [], ['note'], ['cdata']));
    }

    /**
     * @throws \Exception
     */
    public function testGetAmountNodesOnLevel1(): void
    {
        // document_xml-no_content.xml: Word document.xml,
        // that has no content other than a basic document.xml structure

        /** @noinspection ReturnFalseInspection */
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/document_xml-no_content.xml');

        self::assertEquals(2, HelperXml::getAmountNodes($xml, [1], []));

        self::assertEquals(0, HelperXml::getAmountNodes($xml, [1], ['W:DOCUMENT']));

        self::assertEquals(0, HelperXml::getAmountNodes($xml, [1], ['W:DOCUMENT', 'W:BODY']));
    }

    /**
     * @throws \Exception
     */
    public function testGetAmountNodesOnAllLevels(): void
    {
        // document_xml-no_content.xml: Word document.xml,
        // that has no content other than a basic document.xml structure

        /** @noinspection ReturnFalseInspection */
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/document_xml-no_content.xml');

        self::assertEquals(3, HelperXml::getAmountNodes($xml, []));

        self::assertEquals(1, HelperXml::getAmountNodes($xml, [], ['W:DOCUMENT']));

        self::assertEquals(0, HelperXml::getAmountNodes($xml, [], ['W:DOCUMENT', 'W:BODY']));
    }

    /**
     * @throws \Exception
     */
    public function testGetAmountNodesByLevel(): void
    {
        // document_xml-no_content.xml: Word document.xml,
        // that has no content other than a basic document.xml structure

        /** @noinspection ReturnFalseInspection */
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/document_xml-no_content.xml');

        self::assertEquals(3, HelperXml::getAmountNodes($xml, []));

        self::assertEquals(2, HelperXml::getAmountNodes($xml, [1]));

        self::assertEquals(1, HelperXml::getAmountNodes($xml, [2]));
    }

    /**
     * @throws \Exception
     */
    public function testGetAmountNodesByType(): void
    {
        // document_xml-no_content.xml: Word document.xml,
        // that has no content other than a basic document.xml structure

        /** @noinspection ReturnFalseInspection */
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/document_xml-no_content.xml');

        self::assertEquals(3, HelperXml::getAmountNodes($xml, []));

        self::assertEquals(2, HelperXml::getAmountNodes($xml, [], [], [HelperXml::TAG_TYPE_OPEN]));

        self::assertEquals(2, HelperXml::getAmountNodes($xml, [], [], [HelperXml::TAG_TYPE_CLOSE]));

        self::assertEquals(2, HelperXml::getAmountNodes($xml, [], [], [HelperXml::TAG_TYPE_COMPLETE]));

        self::assertEquals(
            1,
            HelperXml::getAmountNodes($xml, [], [], [HelperXml::TAG_TYPE_CLOSE, HelperXml::TAG_TYPE_COMPLETE])
        );
    }

    /**
     * @throws \Gyselroth\Helper\Exception\XmlException
     */
    public function testValidateValid(): void
    {
        $xmlPath = __DIR__ . '/Fixtures/data/xml/mock.xml';
        $xsdPath = __DIR__ . '/Fixtures/data/xml/mock.xsd';

        self::assertInstanceOf(\DOMDocument::class, HelperXml::validate($xmlPath, $xsdPath));
    }

//    /**
//     * @throws \Gyselroth\Helper\Exception\XmlException
//     */
//    public function testValidateVersion()
//    {
//        $xmlPath = __DIR__ . '/Fixtures/data/xml/mock.xml';
//        $xsdPath = __DIR__ . '/Fixtures/data/xml/mock.xsd';
//        self::assertTrue(HelperXml::validate($xmlPath, $xsdPath, '0.9')->version == '0.9');
//    }

    /**
     * @throws \Gyselroth\Helper\Exception\XmlException
     */
    public function testValidateInvalid(): void
    {
        $xmlPath = __DIR__ . '/Fixtures/data/xml/mock.xml';
        $xsdPath = __DIR__ . '/Fixtures/data/xml/invalid_mock.xsd';

        self::assertFalse(HelperXml::validate($xmlPath, $xsdPath));
    }

    /**
     * @expectedException        \Gyselroth\Helper\Exception\XmlException
     * @expectedExceptionMessage XSD
     */
    public function testValidateMissingXsd(): void
    {
        $xmlPath = __DIR__ . '/Fixtures/data/xml/mock.xml';
        $xsdPath = __DIR__ . '/invalid/path/to/mock.xsd';

        HelperXml::validate($xmlPath, $xsdPath);
    }

    /**
     * @expectedException        \Gyselroth\Helper\Exception\XmlException
     * @expectedExceptionMessage XML
     */
    public function testValidateMissingXml(): void
    {
        $xmlPath = __DIR__ . '/invalid/path/to/mock.xml';
        $xsdPath = __DIR__ . '/Fixtures/data/xml/mock.xsd';

        HelperXml::validate($xmlPath, $xsdPath);
    }

//    public function testDebugPrint(): void
//    {
//        $xmlPath = __DIR__ . '/Fixtures/data/xml/mock.xml';
//
//        $xml = new \DOMDocument('1.0', 'UTF-8');
//        $xml->load($xmlPath);
//
//        \ob_start();
//
//        HelperXml::debugPrint($xml);
//
//        $debugPrint = \ob_end_clean();
//
//        self::assertEquals($debugPrint, \file_get_contents($xmlPath));
//    }

    public function testStrReplaceNodeValues(): void
    {
        $xmlPath = __DIR__ . '/Fixtures/data/xml/mock.xml';

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->load($xmlPath);

        $xmlPath2 = __DIR__ . '/Fixtures/data/xml/mock2.xml';

        $xml2 = new \DOMDocument('1.0', 'UTF-8');
        $xml2->load($xmlPath2);

        self::assertEquals(
            $xml2,
            HelperXml::strReplaceNodeValues(
                ['Gyselroth','Ewald','Reminder','I am'],
                ['Philippe','Kay','Confirmation','He is'],
                $xml
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function testGetTagsFromXml(): void
    {
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/mock.xml');
        $tags = HelperXml::getTagsFromXml($xml);

        self::assertSame('NOTE', $tags[0]['tag']);

        self::assertSame('open', $tags[0]['type']);

        self::assertSame('complete', $tags[1]['type']);

        self::assertSame('cdata', $tags[2]['type']);

        self::assertSame('I am currently testing.', $tags[7]['value']);
    }

    public function testFormatXmlString(): void
    {
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/mock3.xml');
        $xml2 = file_get_contents(__DIR__ . '/Fixtures/data/xml/mock.xml');

        self::assertEquals($xml2, HelperXml::formatXmlString($xml));
    }

    public function testXmlNodeToArray(): void
    {
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/mock.xml');
        $xmlArray = HelperXml::xmlNodeToArray(simplexml_load_string($xml));

        self::assertSame('Gyselroth', $xmlArray['to']);

        self::assertSame('I am currently testing.', $xmlArray['body']);
    }
}
