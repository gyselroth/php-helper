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

/**
 * Perl regular expression helper
 */
class HelperPreg
{
    public const LOG_CATEGORY = 'pregHelper';

    /**
     * @param  string    $pattern
     * @param  bool|null $caseInsensitive
     * @param  string    $delimiter
     * @return string Given pattern wrapped into pattern delimiters and options if any
     * @todo add more option arguments
     */
    public static function addPregDelimiters(string $pattern, $caseInsensitive = null, $delimiter = '/'): string
    {
        $options = $caseInsensitive === true ? 'i' : '';

        return $delimiter . $pattern . $delimiter . $options;
    }

    /**
     * @param  string $str
     * @param  string $needlePattern Perl regular expression
     * @param  int    $offset
     * @return int First offset of $needlePatten in $str or -1 if not found
     * @throws PregExceptionEmptyExpression
     */
    public static function pregStrPos(string $str, string $needlePattern, int $offset = 0): int
    {
        if ('' === $needlePattern) {
            throw new PregExceptionEmptyExpression('Empty regular expression');
        }

        if (0 < $offset) {
            $str = \substr($str, $offset);
        }

        \preg_match ($needlePattern, $str, $matches, PREG_OFFSET_CAPTURE);

        if (0 === $offset) {
            return $matches[0][1] ?? -1;
        }

        return $matches[0][1] ? $matches[0][1] + $offset : -1;
    }

    /**
     * @param  string $str
     * @param  string $patternLhs
     * @param  string $patternRhs
     * @return string Given string w/o the 1st sub-string enclosed by given left- and right-hand-side delimiters (delimiters are removed as well)
     */
    public static function pregRemoveBetween(string $str, string $patternLhs, string $patternRhs): string
    {
        return self::pregReplaceBetween($str, $patternLhs, $patternRhs, '');
    }

    /**
     * @param  string $str
     * @param  string $patternLhs
     * @param  string $patternRhs
     * @param  string $replacement
     * @return string Replace 1st occurrence of delimiters matching given regex patterns and their enclosed content by given replacement
     */
    public static function pregReplaceBetween(
        string $str,
        string $patternLhs,
        string $patternRhs,
        string $replacement
    ): string
    {
        // Find consecutive offsets of left- and right-hand-side patterns
        $matchesLhs = self::preg_match_all_with_offsets($patternLhs, $str);
        if ([] === $matchesLhs) {
            return $str;
        }
        $offsetLhs  = \array_keys($matchesLhs)[0];
        $matchLhs   = $matchesLhs[$offsetLhs] ?: '';
        if ($matchLhs === '') {
            return $str;
        }

        $offsetsRhs = self::preg_match_all_with_offsets($patternRhs, $str);
        if ([] === $offsetsRhs) {
            return $str;
        }

        $matchRhs  = '';
        $offsetRhs = -1;
        foreach ($offsetsRhs as $offsetCurrentRhs => $matchCurrentRhs) {
            if ($offsetLhs < $offsetCurrentRhs) {
                $offsetRhs = $offsetCurrentRhs;
                $matchRhs  = $matchCurrentRhs;
                break;
            }
        }

        // Perform regular removal using obtained LHS and RHS matches
        if ('' === $matchLhs
            || -1 === $offsetRhs
            || '' === $matchRhs
        ) {
            return $str;
        }

        $needleLength = $offsetRhs - $offsetLhs + \strlen($matchRhs);

        return \substr_replace($str, $replacement, $offsetLhs, $needleLength);
    }

    public static function startsNumeric(string $str): int
    {
        return 1 === \preg_match('/^\d/', $str);
    }

    public static function removeNumericChars(string $str, bool $trim = true): string
    {
        $str = \preg_replace('/\d/', '', $str);

        return $trim ? \trim($str) : $str;
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
        $str = \preg_replace('/[^0-9,.]/', '', $str);

        return $convertToInt ? (int)$str : $str;
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
        return \preg_split('/(?<!^)(?!$)/u', $string);
    }

    /**
     * @param string $pattern
     * @param string $string $string
     * @return array    Keys: Offsets of matches, Values: matches
     * @todo add argument: $offset
     */
    public static function preg_match_all_with_offsets(string $pattern, string $string): array
    {
        /** @var array $matches */
        \preg_match_all($pattern, $string, $matches);
        $fullMatches = \array_shift($matches);
        if (!\is_array($fullMatches)) {
            return [];
        }

        $matchesByOffset = [];
        $offsetBase      = 0;
        foreach ($fullMatches as $match) {
            $offset                                 = \strpos($string, $match);
            $matchesByOffset[$offsetBase + $offset] = $match;

            $string     = \substr($string, $offset + 1);
            $offsetBase += $offset + 1;
        }

        return $matchesByOffset;
    }
}
