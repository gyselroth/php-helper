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

/**
 * Server/Client helpers: Environment settings, MVC, AJAX
 */
class HelperSanitize
{
    private const LOG_CATEGORY_REQUEST = 'sanitize';

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
            $str);
    }

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

    public static function sanitizeFilename(string $filename): string
    {
        return HelperFile::sanitizeFilename($filename);
    }
}
