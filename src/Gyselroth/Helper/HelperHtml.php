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

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * HTML helper methods
 */
class HelperHtml
{
    public const TEXT_ALIGN_VALUES = ['left', 'right', 'center', 'justify', 'initial', 'inherit'];

    /**
     * Decode until no more special chars are found
     *
     * @param  string $text
     * @return string
     */
    public static function decodeHtmlSpecialChars(string $text): string
    {
        while (static::containsEncodedHtmlSpecialChars($text)) {
            $text = \htmlspecialchars_decode($text);
        }

        return $text;
    }

    /**
     * Check if a string contains any encoded special chars
     *
     * @param  string $text
     * @return bool
     */
    public static function containsEncodedHtmlSpecialChars(string $text): bool
    {
        return $text !== \htmlspecialchars_decode($text);
    }

    /**
     * @param  string $string
     * @return string   Given string w/ variations of <br> tag converted to newlines
     */
    public static function br2nl(string $string): string
    {
        return \str_ireplace(['<br />', '<br/>', '<br>', '<br >'], "\n", $string);
    }

    /**
     * @param  string $string
     * @return string           Given string w/ all inline URLs converted to HTML hyperlinks
     */
    public static function urlsToHyperlinks(string $string): string
    {
        return \preg_replace(
            '/(http[s]?:\/\/\S{4,})\s*/im',
            '<a href="$1" target="_blank">$1</a> ',
            $string);
    }

    public static function stripHtmlTags(string $html, bool $decodeEntity = false): string
    {
        $text = \htmlspecialchars_decode($html);
        $text = \str_replace(["\n", "\r"], '', $text);
        $text = self::br2nl($text);
        $text = \str_replace(['</p>', '</li>', '<li>'], ["\n\n", "\n", ' - '], $text);
        $text = \strip_tags($text);

        if ($decodeEntity) {
            $text = \html_entity_decode($text, ENT_COMPAT, 'UTF-8');
        }

        return \trim($text);
    }

    /**
     * @param  string $html
     * @return string       Given string stripped of all HTML tags, cleaned-up for readability
     */
    public static function html2plaintext(string $html): string
    {
        $plaintext = self::stripHtmlTags($html);
        /** @noinspection ReturnFalseInspection */
        while (false !== \strpos($plaintext, '  ')) {
            $plaintext = \str_replace('  ', ' ', $plaintext);
        }
        /** @noinspection ReturnFalseInspection */
        while (false !== \strpos($plaintext, "\n\n")) {
            $plaintext = \str_replace("\n\n", "\n", $plaintext);
        }

        return $plaintext;
    }

    /**
     * @param  string    $html
     * @param  int|float $widthFactor
     * @param  int|float $heightFactor
     * @return string Given HTML w/ inline style like "width:(\d)+px" and "height:(\d)+px" multiplied by given factors
     */
    public static function resizeStyles(string $html, $widthFactor = 1, $heightFactor = 1): string
    {
        \preg_match_all('/width:(\s)*(\d)+(\w*)(;)*/', $html, $widths);
        \preg_match_all('/height:(\s)*(\d)+(\w*)(;)*/', $html, $heights);

        foreach ($widths[0] as $index=>$width) {
            $html = \str_replace(
                $width,
                'width:' . ($widthFactor * HelperString::removeNonNumericChars($width)) . $widths[3][$index] . ';',
                $html);
        }

        foreach ($heights[0] as $index=>$height) {
            $html = \str_replace(
                $height,
                'height:' . ($heightFactor * HelperString::removeNonNumericChars($height)) . $heights[3][$index] . ';',
                $html);
        }

        return $html;
    }

    public static function umlautsToHtmlEntities(string $html): string
    {
        return \str_replace(
            ['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü'],
            ['&auml;', '&ouml;', '&uuml;', '&Auml;', '&Ouml;', '&Uuml;'],
            $html
        );
    }

    /**
     * Use HTMLPurifier to clean given HTML string
     *
     * @param  string $html
     * @param  bool   $enableTargetBlank
     * @param  bool   $escapeSingleQuotes
     * @param  bool   $escapeBackslashes
     * @param  bool   $disablePurifierCache
     * @param  bool   $allowVideo
     * @return string
     */
    public static function getCleanedHtml(
        string $html,
        bool $enableTargetBlank = false,
        bool $escapeSingleQuotes = false,
        bool $escapeBackslashes = false,
        bool $disablePurifierCache = true,
        bool $allowVideo = false
    ): string
    {
        $config = HTMLPurifier_Config::createDefault();

        if ($enableTargetBlank) {
            // Allow target: _blank for open link in new window
            $config->set('Attr.AllowedFrameTargets', array('_blank'));
        }
        if ($disablePurifierCache) {
            // @note    Setting "Core.DefinitionCache" will trigger a PHP error: "Core.DefinitionCache" is an alias for "Cache.DefinitionImpl"
            $config->set('Cache.DefinitionImpl', null);
        }
        if ($allowVideo) {
            // Allow video: only if url is from YouTube or Vimeo
            $config->set('HTML.SafeIframe', true);
            // Allow fullScreen for videos, with custom HTML purifier filter
            $config->set('Filter.Custom', array(new HelperHtmlPurifierIframeFilter()));
            $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');
        }

        $html = (new HTMLPurifier($config))->purify($html);

        if ($escapeSingleQuotes) {
            // Escape single quotes to prevent JavaScript error
            $html = \str_replace("'", '&#39;', $html);
        }
        if ($escapeBackslashes) {
            // Escape backslashes to prevent loosing them
            $html = \str_replace('\\', '\\\\', $html);
        }

        return $html;
    }

    /**
     * Compacts dump of PHP-array (or object) created e.g. via print_r (removes unnecessary linebreaks)
     *
     * @param string $dump
     * @return string
     */
    public static function formatArrayDump(string $dump): string
    {
        $dump = \preg_replace('/Array\s*\n\s*/', 'array', $dump);

        return \preg_replace('/\)\s*\n\n/', ")\n", $dump);
    }

    public static function renderTableHead(array $columns): string
    {
        $cells = '';
        foreach ($columns as $columnContent) {
            $cells .= '<th>' . $columnContent . '</th>';
        }

        return '<thead><tr>' . $cells . '</tr></thead>';
    }

    public static function validateTextAlignValue(string $value): string
    {
        return \in_array($value, self::TEXT_ALIGN_VALUES, true)
            ? $value
            : 'left';
    }
}
