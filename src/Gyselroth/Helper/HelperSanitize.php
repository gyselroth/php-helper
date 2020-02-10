<?php

/**
 * Copyright (c) 2017-2020 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Gyselroth\Helper;

use Gyselroth\Helper\Interfaces\ConstantsEntitiesOfStrings;

/**
 * Server/Client helpers: Environment settings, MVC, AJAX
 */
class HelperSanitize implements ConstantsEntitiesOfStrings
{
    /**
     * @param  array|string $idOrIDs
     * @param  string $glue
     * @return array|int|string
     */
    public static function sanitizeIdSingleOrMultiple($idOrIDs, string $glue = ',')
    {
        if (\is_array($idOrIDs)) {
            return HelperArray::intVal($idOrIDs);
        }

        return \is_numeric($idOrIDs)
            ? (int)$idOrIDs
            : self::sanitizeIDs($idOrIDs, $glue);
    }

    public static function sanitizeIDs(string $value, string $glue = ','): string
    {
        return \implode($glue, HelperArray::intExplode($value, $glue));
    }

    /**
     * @param string $str
     * @param bool $allowUnderscore
     * @return string|null   Given string w/o characters that are not a-z / A-Z / 0-9 or optionally allowed characters
     */
    public static function filterAlphaNumeric(string $str, bool $allowUnderscore = false) : ?string
    {
        return \preg_replace(
            '/[^a-zA-Z0-9'
            . ($allowUnderscore ? '_' : '') . ']+/',
            '',
            $str
        );
    }

    public static function sanitizeString(
        string $str,
        bool $allowCharacters = true,
        bool $allowUmlauts = false,
        bool $allowDigits = false,
        bool $allowWhiteSpace = false,
        bool $allowSpace = false,
        string $allowedSpecialCharacters = ''
    ): bool
    {
        $regExpression = '';

        if ($allowCharacters) {
            $regExpression .= 'A-Za-z';
        }

        if ($allowDigits) {
            $regExpression .= '0-9';
        }

        if ($allowWhiteSpace) {
            $regExpression .= '\s';
        } elseif ($allowSpace) {
            $regExpression .= ' ';
        }

        if ($allowUmlauts) {
            $regExpression .= \implode('', self::UMLAUTS);
        }

        if ('' !== $allowedSpecialCharacters) {
            $regExpression .= $allowedSpecialCharacters;
        }

        return (bool)\preg_match('/[' . $regExpression . ']+/', $str);
    }

    public static function sanitizeFilename(
        string $filename,
        bool $toLower = true,
        bool $specialCharsToAscii = true
    ): string
    {
        // Convert space to hyphen, remove single- and double- quotes
        $filename = \str_replace([' ', '\'', '"'], ['-', '', ''], $filename);

        if ($specialCharsToAscii) {
            $filename = HelperString::specialCharsToAscii($filename, $toLower);
        }

        // Remove non-word chars (leaving hyphens and periods)
        $filename = \preg_replace('/[^\w\-.]+/', '', $filename);

        // Reduce multiple hyphens to one
        $filename = \preg_replace('/[\-]+/', '-', $filename);

        return HelperString::reduceCharRepetitions($filename, ['.', '_', '-']);
    }

    /**
     * @param string $str
     * @param bool $allowCharacters
     * @param bool $allowUmlauts
     * @param bool $allowDigits
     * @param bool $allowWhiteSpace
     * @param bool $allowSpace
     * @param string $allowedSpecialCharacters
     * @return bool
     *
     * improved version of validateString consider using this one
     */
    public static function validateStringImproved(
        string $str,
        bool $allowCharacters = true,
        bool $allowUmlauts = false,
        bool $allowDigits = false,
        bool $allowWhiteSpace = false,
        bool $allowSpace = false,
        string $allowedSpecialCharacters = ''
    ): bool
    {
        $regExpression = '';

        if ($allowCharacters) {
            $regExpression .= 'A-Za-z';
        }

        if ($allowDigits) {
            $regExpression .= '0-9';
        }

        if ($allowWhiteSpace) {
            $regExpression .= '\s';
        } elseif ($allowSpace) {
            $regExpression .= ' ';
        }

        if ($allowUmlauts) {
            $regExpression .= \implode('', self::UMLAUTS);
        }

        if ('' !== $allowedSpecialCharacters) {
            $regExpression .= $allowedSpecialCharacters;
        }

        return !(bool)\preg_match('/[^' . $regExpression . ']+/', $str);
    }

    /**
     * @param string $str
     * @param bool $allowCharacters
     * @param bool $allowUmlauts
     * @param bool $allowDigits
     * @param bool $allowWhiteSpace
     * @param bool $allowSpace
     * @param string $allowedSpecialCharacters
     * @return bool
     * @deprecated Method is useless at the moment consider using validateStringImproved
     */
    public static function validateString(
        string $str,
        bool $allowCharacters = true,
        bool $allowUmlauts = false,
        bool $allowDigits = false,
        bool $allowWhiteSpace = false,
        bool $allowSpace = false,
        string $allowedSpecialCharacters = ''
    ): bool
    {
        return self::sanitizeString(
            $str,
            $allowCharacters,
            $allowUmlauts,
            $allowDigits,
            $allowWhiteSpace,
            $allowSpace,
            $allowedSpecialCharacters
        );
    }
}
