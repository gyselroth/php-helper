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

use Gyselroth\Helper\Exception\PregExceptionEmptyExpression;
use Gyselroth\Helper\Interfaces\ConstantsDataTypesInterface;

class HelperString implements ConstantsDataTypesInterface
{
    public const LOG_CATEGORY = 'stringHelper';

    // Character classes
    public const CHAR_TYPE_ALPHA_LOWER = 0;
    public const CHAR_TYPE_ALPHA_UPPER = 1;
    public const CHAR_TYPE_NUMBER      = 2;
    public const CHAR_TYPE_SPECIAL     = 3;

    /**
     * @param      string $haystack
     * @param      array  $needles
     * @return     array|false
     * @deprecated use instead: strposConsecutive()
     */
    public static function strPosMultiple(string $haystack, array $needles) {
        return self::strPosConsecutive($haystack, $needles);
    }

    /**
     * Find successive sub-string offsets
     *
     * @param  string $haystack
     * @param  array  $needles
     * @param  bool   $associative Return as associative array ['needle1' => $offset1, 'needle2' => ...] (default) or as indexed array?
     * @return array|false          Array w/ found offset of each needle or false if none is contained in $haystack
     */
    public static function strPosConsecutive(string $haystack, array $needles, bool $associative = true)
    {
        $offsets     = \array_flip($needles);
        $hasFoundAny = false;
        foreach ($needles as $needle) {
            /** @noinspection ReturnFalseInspection */
            // @todo currently offsets are not consecutive! check: isn't the following strpos() missing an $offset+1 argument?
            $offset = \strpos($haystack, $needle);
            if (false !== $offset) {
                $offsets[$needle] = $offset;
                $hasFoundAny      = true;
            }
        }

        if (!$hasFoundAny) {
            return false;
        }

        return $associative ? $offsets : \array_values($offsets);
    }

    /**
     * Get string between first occurrence of start and end if both markers are found
     *
     * @param  string $string
     * @param  string $start
     * @param  string $end
     * @param  bool   $trim
     * @return string
     */
    public static function getStringBetween(string $string, string $start, string $end, bool $trim = true): string
    {
        if ('' === $string
            || '' === $start
            || '' === $end
        ) {
            return '';
        }

        /** @noinspection ReturnFalseInspection */
        $offset = \strpos($string, $start);
        /** @noinspection ReturnFalseInspection */
        if (false === $offset
            || false === \strpos($string, $end)
        ) {
            return '';
        }

        $offset  += \strlen($start);

        // @todo check if $offset is not contained, $length will be negative. check how this can happen and to what consequence, avoid that possible error.
        $length  = \strpos($string, $end, $offset) - $offset;
        $between = \substr($string, $offset, $length);

        return $trim ? \trim($between) : $between;
    }

    /**
     * @param  string       $haystack
     * @param  array|string $needle
     * @return bool
     */
    public static function startsWith(string $haystack, $needle): bool
    {
        if ('' === $needle) {
            return true;
        }
        if (!\is_array($needle)) {
            /** @noinspection ReturnFalseInspection */
            return 0 === \strpos($haystack, $needle);
        }

        // Needle is array (of needles): check whether haystack starts with any of them
        /** @noinspection ForeachSourceInspection */
        foreach ($needle as $needleString) {
            /** @noinspection ReturnFalseInspection */
            if (0 === \strpos($haystack, $needleString)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string       $haystack
     * @param  array|string $needles
     * @return bool         Haystack ends w/ given (or any of the multiple given) needle(s)?
     */
    public static function endsWith(string $haystack, $needles): bool
    {
        if (!\is_array($needles)) {
            return '' === $needles
                || \substr($haystack, -\strlen($needles)) === $needles;
        }

        /** @noinspection ForeachSourceInspection */
        foreach ($needles as $needle) {
            if (self::endsWith($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function replaceFirst(string $subject, string $search, string $replace = ''): string
    {
        if (\strlen($search) > 0) {
            /** @noinspection ReturnFalseInspection */
            $offset = \strpos($subject, $search);
            if (false !== $offset) {
                return \substr_replace($subject, $replace, $offset, \strlen($search));
            }
        }

        return $subject;
    }

    public static function replaceLast(string $search, string $replace, string $subject): string
    {
        /** @noinspection ReturnFalseInspection */
        $offset = \strrpos($subject, $search);

        return false !== $offset
            ? \substr_replace($subject, $replace, $offset, \strlen($search))
            : $subject;
    }

    /**
     * @param  string $str
     * @param  string $lhs                   "left-hand-side" (prefix to be prepended)
     * @param  string $rhs                   "right-hand-side" (postfix to be appended)
     * @param  bool   $preventDoubleWrapping Prevent wrapping into already existing LHS / RHS?
     * @return string                       Given string wrapped into given LHS / RHS
     */
    public static function wrap(string $str, string $lhs, string $rhs, bool $preventDoubleWrapping = true): string
    {
        return $preventDoubleWrapping
            ? (static::startsWith($str, $lhs) ? '' : $lhs) . $str . (static::endsWith($str, $rhs) ? '' : $rhs)
            : $lhs . $str . $rhs;
    }

    /**
     * @param  string $needle
     * @param  string $str
     * @param  int    $offsetNeedle
     * @param  bool   $excludeNeedle
     * @return string Substring of given string starting from $offsetNeedle'th occurrence of needle
     */
    public static function removeAllBefore(
        string $needle,
        string $str,
        int $offsetNeedle = 0,
        bool $excludeNeedle = false
    ): string
    {
        if ($offsetNeedle > \strlen($str)) {
            return $str;
        }
        // @todo check what happens when $needle is not contained after $offsetNeedle, $start will be false
        $start = \strpos($str, $needle, $offsetNeedle);

        return \substr($str, $start + ($excludeNeedle ? \strlen($needle) : 0));
    }

    public static function removeAllAfter(
        string $needle,
        string $str,
        int $offsetNeedle = 0,
        bool $excludeNeedle = false
    ): string
    {
        if ($offsetNeedle > \strlen($str)) {
            return $str;
        }

        /** @noinspection ReturnFalseInspection */
        $start = \strpos($str, $needle, $offsetNeedle);

        return \substr($str, 0, $start + ($excludeNeedle ? 0 : \strlen($needle)));
    }

    /**
     * @param  string $str
     * @param  string $lhs
     * @param  string $rhs
     * @param  bool   $removeDelimiters
     * @return string Given string w/o the 1st sub-string enclosed by given left- and right-hand-side delimiters
     */
    public static function removeAllBetween(string $str, string $lhs, string $rhs, $removeDelimiters = true): string
    {
        if ('' === $str) {
            return $str;
        }
        /** @noinspection ReturnFalseInspection */
        $offsetLhs = \strpos($str, $lhs);
        if (false === $offsetLhs) {
            return $str;
        }

        /** @noinspection ReturnFalseInspection */
        $offsetRhs = \strpos($str, $rhs, $offsetLhs + 1);
        /** @noinspection PhpUnreachableStatementInspection */
        if (false === $offsetRhs) {
            return $str;
        }

        $needleLengthWithoutDelimiters = $offsetRhs - ($offsetLhs + \strlen($lhs));

        $needleLength = $needleLengthWithoutDelimiters + ($removeDelimiters
            // W/ delimiters: add length of delimiters
            ? \strlen($lhs) + \strlen($rhs)
            // W/o delimiters
            : 0
        );

        return \substr_replace(
            $str,
            '',
            $offsetLhs + ($removeDelimiters ? 0 : \strlen($lhs)),
            $needleLength
        );
    }

    public static function unwrap(string $str, string $lhs, string $rhs): string
    {
        /** @noinspection ReturnFalseInspection */
        if (0 === \strpos($str, $lhs)) {
            // Remove left-hand-side wrap
            $str = \substr($str, \strlen($lhs));
        }

        if (self::endsWith($str, $rhs)) {
            // Remove right-hand-side wrap
            $str = \substr($str, 0, -\strlen($rhs));
        }

        return $str;
    }

    public static function formatJsonCompatible(string $string): string
    {
        return \str_replace(["\n", "\r", "'"], ['', '', '"'], $string);
    }

    public static function isXml(string $str): bool
    {
        return HelperXml::isValidXml($str);
    }

    public static function isUtf8(string $str): bool
    {
        return \strlen($str) > \strlen(utf8_decode($str));
    }

    public static function sanitizeFilename(string $filename): string
    {
        return HelperFile::sanitizeFilename($filename);
    }

    /**
     * Reduce all repetitions of the given character(s) inside the given string to a single occurrence
     *
     * @param  string       $string
     * @param  string|array $characters
     * @return string
     */
    public static function reduceCharRepetitions(string $string, $characters): string
    {
        if (\is_array($characters)) {
            foreach ($characters as $currentCharacter) {
                $string = static::reduceCharRepetitions($string, $currentCharacter);
            }
        } else {
            $double = $characters . $characters;
            /** @noinspection ReturnFalseInspection */
            while (false !== \strpos($string, $double)) {
                $string = \str_replace($double, $characters, $string);
            }
        }

        return $string;
    }

    /**
     * Convert string to camelCase
     *
     * @param  string $string
     * @param  bool   $upperCaseFirstLetter
     * @return string
     */
    public static function toCamelCase(string $string, bool $upperCaseFirstLetter = false): string
    {
        if ($upperCaseFirstLetter) {
            $string = \ucfirst($string);
        }

        return \preg_replace_callback(
            '/-([a-z])/',
            static function($c) {
                return \strtoupper($c[1]);
            },
            $string
        );
    }

    /**
     * @param  string $camelString
     * @param  string $glue
     * @return string Minus separated path string from given camel-cased string
     */
    public static function getPathFromCamelCase(string $camelString, string $glue = '-'): string
    {
        $string = \preg_replace(
            '/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/',
            $glue . '$0',
            \lcfirst($camelString));

        return \strtolower($string);
    }

    /**
     * Generate random string
     *
     * @param  int    $length
     * @param  bool   $containAlphaLower
     * @param  bool   $containAlphaUpper
     * @param  bool   $containNumbers
     * @param  string $specialChars
     * @param  bool   $eachSpecialCharOnlyOnce
     * @return string
     * @throws \Exception
     */
    public static function getRandomString(
        int $length = 8,
        bool $containAlphaLower = true,
        bool $containAlphaUpper = false,
        bool $containNumbers = true,
        string $specialChars = '',
        bool $eachSpecialCharOnlyOnce = true
    ): string
    {
        $str    = '';
        $offset = 0;

        $charTypes = [];
        if ($containAlphaLower) {
            $charTypes[] = static::CHAR_TYPE_ALPHA_LOWER;
        }
        if ($containAlphaUpper) {
            $charTypes[] = static::CHAR_TYPE_ALPHA_UPPER;
        }
        if ($containNumbers) {
            $charTypes[] = static::CHAR_TYPE_NUMBER;
        }
        if (!empty($specialChars)) {
            $charTypes[] = static::CHAR_TYPE_SPECIAL;
        }

        $amountTypes = \count($charTypes);
        while (\strlen($str) < $length) {
            $typeOffset = $offset % $amountTypes;
            switch ($charTypes[$typeOffset]) {
                case static::CHAR_TYPE_ALPHA_LOWER:
                    $str .= static::getRandomLetter();
                    break;
                case static::CHAR_TYPE_ALPHA_UPPER:
                    $str .= static::getRandomLetter(true);
                    break;
                case static::CHAR_TYPE_NUMBER:
                    $str .= \random_int(0, 9);
                    break;
                case static::CHAR_TYPE_SPECIAL:
                    $specialChar = static::getRandomLetter(false, $specialChars);

                    if ($eachSpecialCharOnlyOnce) {
                        $specialChars = \str_replace($specialChar, '', $specialChars);
                        if (empty($specialChars)) {
                            unset($charTypes[$typeOffset]);
                            $amountTypes--;
                        }
                    }
                    $str .= $specialChar;
                    break;
                default:
                    LoggerWrapper::warning(
                        __CLASS__ . '::' . __FUNCTION__ . " - Unknown char type: {$charTypes[$typeOffset]}",
                        [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY, LoggerWrapper::OPT_PARAMS => $charTypes[$typeOffset]]);
                    break;
            }
            $offset++;
        }

        return $str;
    }

    /**
     * @param  bool   $upperCase
     * @param  string $pool Pool of allowed random characters
     * @return string
     */
    public static function getRandomLetter(
        bool $upperCase = false,
        string $pool = 'abcdefghijklmnopqrstuvwxyz'
    ): string
    {
        if (1 === \strlen($pool)) {
            return $upperCase ? \strtoupper($pool) : $pool;
        }

        $str = \substr(
            \str_shuffle(
                \str_repeat($pool, 5)
            ),
            0,
            1);

        return $upperCase ? \strtoupper($str) : $str;
    }

    /**
     * Generate alphabetical string from given character index:
     *
     * 0  = 'a', 1 = 'b', ...,
     * 25 = 'z'
     * 26 = 'aa' (when index > 25: use character of index mod 25, repeated as many times as there are modulo "wrap-arounds")
     *
     * @param  int $characterIndex
     * @return string|null
     */
    public static function toAlpha(int $characterIndex): ?string
    {
        $letters = \range('a', 'z');
        if ($characterIndex <= 25) {
            return $letters[$characterIndex];
        }

        $dividend       = $characterIndex + 1;
        $alphaCharacter = '';
        while ($dividend > 0) {
            $modulo         = ($dividend - 1) % 26;
            $alphaCharacter = $letters[$modulo] . $alphaCharacter;
            $dividend       = \floor(($dividend - $modulo) / 26);
        }

        return $alphaCharacter;
    }

    /**
     * Encode string with base64 and make it save for using in URLs
     *
     * @param  string $string
     * @return string
     */
    public static function urlSafeB64encode(string $string): string
    {
        return \str_replace(
            ['+', '/', '='],
            ['-', '_', '.'],
            \base64_encode($string));
    }

    public static function urlSafeB64Decode(string $string): string
    {
        $data = \str_replace(
            ['-', '_', '.'],
            ['+', '/', '='],
            $string);

        $mod4 = \strlen($data) % 4;

        if ($mod4) {
            $data .= \substr('====', $mod4);
        }

        /** @noinspection ReturnFalseInspection */
        return $data
            ? \base64_decode($data)
            : '';
    }

    /**
     * @param  int|string $value
     * @param  int|string $conditionValue
     * @param  string     $operatorString
     * @param  bool       $strict
     * @return bool
     */
    public static function compareValuesByComparisonOperators(
        $value,
        $conditionValue,
        $operatorString = null,
        bool $strict = false
    ): ?bool
    {
        switch ($operatorString) {
            case self::OPERATOR_LESS_THAN:
                return $value < $conditionValue;
            case self::OPERATOR_LESS_OR_EQUAL:
                return $value <= $conditionValue;
            case self::OPERATOR_GREATER_THAN:
                return $value > $conditionValue;
            case self::OPERATOR_EQUAL:
                if ($strict) {
                    return $value === $conditionValue;
                }

                /** @noinspection TypeUnsafeComparisonInspection */
                return $value == $conditionValue;
            case self::OPERATOR_GREATER_OR_EQUAL:
            default:
                return $value >= $conditionValue;
        }
    }

    /**
     * @param  string       $str
     * @param  array|string $needles
     * @return bool Contains any of 1. (if $needles is string) the characters in $needles, 2. any of the given $needles
     */
    public static function containsAnyOf(string $str, $needles): bool
    {
        if (!\is_array($needles)) {
            $needles = HelperPreg::mb_str_split($needles);
        }

        /** @noinspection ForeachSourceInspection */
        foreach ($needles as $needle) {
            /** @noinspection ReturnFalseInspection */
            if (false !== \strpos($str, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert output of var_dump() into serialized representation
     *
     * @param  string $str
     * @return string
     */
    public static function serialize_dump(string $str): string
    {
        /** @noinspection ReturnFalseInspection */
        if (false === \strpos($str, "\n")) {
            // Add new lines
            $regex = [
                '#(\\[.*?\\]=>)#',
                '#(string\\(|int\\(|float\\(|array\\(|NULL|object\\(|})#',
            ];
            $str   = \preg_replace($regex, "\n\\1", $str);
            $str   = \trim($str);
        }

        $serialized = \preg_replace(
            [
                '#^\\040*NULL\\040*$#m',
                '#^\\s*array\\((.*?)\\)\\s*{\\s*$#m',
                '#^\\s*string\\((.*?)\\)\\s*(.*?)$#m',
                '#^\\s*int\\((.*?)\\)\\s*$#m',
                '#^\\s*bool\\(true\\)\\s*$#m',
                '#^\\s*bool\\(false\\)\\s*$#m',
                '#^\\s*float\\((.*?)\\)\\s*$#m',
                '#^\\s*\[(\\d+)\\]\\s*=>\\s*$#m',
                '#\\s*?\\r?\\n\\s*#m',
            ],
            [
                'N',
                'a:\\1:{',
                's:\\1:\\2',
                'i:\\1',
                'b:1',
                'b:0',
                'd:\\1',
                'i:\\1',
                ';'
            ],
            $str);

        $serialized = \preg_replace_callback(
            '#\\s*\\["(.*?)"\\]\\s*=>#',
            static function($match) {
                return 's:' . \strlen($match[1]) . ':\"' . $match[1] . '\"';
            },
            $serialized
        );

        $serialized = \preg_replace_callback(
            '#object\\((.*?)\\).*?\\((\\d+)\\)\\s*{\\s*;#',
            static function($match) {
                return 'O:'
                    . strlen($match[1]) . ':\"'
                    . $match[1] . '\":'
                    . $match[2] . ':{';
            },
            $serialized
        );

        return \preg_replace(
            ['#};#', '#{;#'],
            ['}', '{'],
            $serialized);
    }


    /**
     * Convert output of var_dump() back into PHP value
     *
     * @param  string $str
     * @return array|bool|float|int|Object|string
     */
    public static function unVar_dump(string $str)
    {
        $serialized = self::serialize_dump($str);

        /** @noinspection UnserializeExploitsInspection */
        return \unserialize($serialized);
    }

    public static function translate(string $message, array $args = []): string
    {
        return [] === $args
            ? $message
            : \vsprintf($message, $args);
    }

    public static function translatePlural(string $single, string $multiple, int $amount): string
    {
        return $amount === 0
        || $amount > 1
            ? $multiple
            : $single;
    }

    // ------------------------------------------------- Convenience-Wrappers to Perl regular expression related helpers

    /**
     * @param  string    $pattern
     * @param  bool|null $caseInsensitive
     * @return string
     */
    public static function addPregDelimiters(string $pattern, $caseInsensitive = null): string
    {
        return HelperPreg::addPregDelimiters($pattern, $caseInsensitive);
    }

    public static function startsNumeric(string $str): bool
    {
        return HelperPreg::startsNumeric($str);
    }

    public static function removeNumericChars(string $str, bool $trim = true): string
    {
        return HelperPreg::removeNumericChars($str, $trim);
    }

    /**
     * Reduce given string to its contained numbers
     *
     * @param  string    $str
     * @param  bool|null $convertToInt
     * @return string|int
     */
    public static function removeNonNumericChars(string $str, bool $convertToInt = false)
    {
        return HelperPreg::removeNonNumericChars($str, $convertToInt);
    }

    /**
     * @param  string $str
     * @param  string $needlePattern Perl regular expression
     * @param  int    $offset
     * @return int First offset of $needlePatten in $str or -1 if not found
     * @throws PregExceptionEmptyExpression
     */
    public static function pregStrPos(string $str, string $needlePattern, $offset = 0): int
    {
        return HelperPreg::pregStrPos($str, $needlePattern, $offset);
    }

    /**
     * @param  string $str
     * @param  string $patternLhs
     * @param  string $patternRhs
     * @return string Given string w/o the 1st sub-string enclosed by given left- and right-hand-side delimiters (delimiters are removed as well)
     */
    public static function pregRemoveBetween(string $str, string $patternLhs, string $patternRhs): string
    {
        return HelperPreg::pregRemoveBetween($str, $patternLhs, $patternRhs);
    }

    /**
     * @param  string $str
     * @param  string $patternLhs
     * @param  string $patternRhs
     * @param  string $replacement
     * @return string Replace 1st occurrence of delimiters matching given regex patterns and their enclosed content by given replacement
     */
    public static function pregReplaceBetween(string $str, string $patternLhs, string $patternRhs, string $replacement): string
    {
        return HelperPreg::pregReplaceBetween($str, $patternLhs, $patternRhs, $replacement);
    }

    /**
     * Multi-byte (Unicode compatible) explode variant
     *
     * Split at all position not after the start: ^ and not before the end: $
     *
     * @param  string $string
     * @return array
     */
    public static function mb_str_split(string $string): array
    {
        return HelperPreg::mb_str_split($string);
    }

    /**
     * @param string $pattern
     * @param string $string $string
     * @return array    Keys: Offsets of matches, Values: matches
     */
    public static function preg_match_all_with_offsets(string $pattern, string $string): array
    {
        return HelperPreg::preg_match_all_with_offsets($pattern, $string);
    }

    /**
     * @param  int|string $number
     * @param  int        $digits
     * @return string
     * @deprecated
     */
    public static function formatAmountDigits($number, int $digits): string
    {
        return HelperNumeric::formatAmountDigits($number, $digits);
    }

    /**
     * @todo  add option/method(s) to allow also dateTime and/or time
     * @param  string $str
     * @param  string $delimiter
     * @param  bool   $isGermanNotation Validate against german notation (dd.mm.yyyy) instead of gregorian (mm.dd.yyyy)
     * @return bool
     * @deprecated
     */
    public static function isDate(string $str, string $delimiter = '.', bool $isGermanNotation = true): bool
    {
        return HelperDate::isDateString($str, $delimiter, $isGermanNotation);
    }
}
