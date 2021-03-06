<?php

/**
 * Copyright (c) 2017-2020 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace {
    // TODO use psr-4 instead of require_once hotfix
    $pathFilters = __DIR__ . '/../../../..//html-purifier-filters/';

    $pathIframFilter = \is_dir($pathFilters)
        ?  $pathFilters . '/src/Gyselroth'  // Load from installed vendor package
        : __DIR__ . '/../../../vendor/'     // Load from dev installation
        . 'gyselroth/html-purifier-filters/src/Gyselroth';

    require_once $pathIframFilter . '/htmlPurifierFilters/IframeFilter.php';
}

namespace Gyselroth\Helper {
    use Gyselroth\HtmlPurifierFilters\IframeFilter;
    use HTMLPurifier;
    use HTMLPurifier_Config;

    /**
     * HTML helper methods
     */
    class HelperHtml
    {
        public const TEXT_ALIGN_VALUES = ['left', 'right', 'center', 'justify', 'initial', 'inherit'];

        public const IMAGE_SOURCE_PREFIX_JPEG_BASE_64 = 'data:image/jpeg;base64,';

        protected const PATTERN_ATTRIBUTE_WIDTH = '/width:(\s)*(\d)+(\w*)(;)*/';
        protected const PATTERN_ATTRIBUTE_HEIGHT = '/height:(\s)*(\d)+(\w*)(;)*/';

        /**
         * Decode until no more special chars are found
         *
         * @param string $text
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
         * @param string $text
         * @return bool
         */
        public static function containsEncodedHtmlSpecialChars(string $text): bool
        {
            return $text !== \htmlspecialchars_decode($text);
        }

        /**
         * @param string $string
         * @return string   Given string w/ variations of <br> tag converted to newlines
         */
        public static function br2nl(string $string): string
        {
            return \str_ireplace(
                ['<br />', '<br/>', '<br>', '<br >'],
                "\n",
                $string
            );
        }

        /**
         * @param string $string
         * @return string|null      Given string w/ all inline URLs converted to HTML hyperlinks
         */
        public static function urlsToHyperlinks(string $string): ?string
        {
            return \preg_replace(
                '/(http[s]?:\/\/\S{4,})\s*/im',
                '<a href="$1" target="_blank">$1</a> ',
                $string
            );
        }

        public static function stripHtmlTags(string $html, bool $decodeEntity = false): string
        {
            $text = \htmlspecialchars_decode($html);
            $text = \str_replace(["\n", "\r"], '', $text);
            $text = self::br2nl($text);

            $text = \str_replace(
                ['</p>', '</li>', '<li>'],
                ["\n\n", "\n", ' - '],
                $text
            );

            $text = \strip_tags($text);

            if ($decodeEntity) {
                $text = \html_entity_decode($text, ENT_COMPAT, 'UTF-8');
            }

            return \trim($text);
        }

        /**
         * @param string $html
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
         * @param string $html
         * @param int|float $widthFactor
         * @param int|float $heightFactor
         * @return string Given HTML w/ inline style like
         *                "width:(\d)+px" and "height:(\d)+px" multiplied by given factors
         */
        public static function resizeStyles(string $html, $widthFactor = 1, $heightFactor = 1): string
        {
            \preg_match_all(self::PATTERN_ATTRIBUTE_WIDTH, $html, $widths);
            \preg_match_all(self::PATTERN_ATTRIBUTE_HEIGHT, $html, $heights);

            foreach ($widths[0] as $index => $width) {
                $html = \str_replace(
                    $width,
                    'width:'
                    . ($widthFactor * (int)HelperPreg::removeNonNumericChars($width))
                    . $widths[3][$index]
                    . ';',
                    $html
                );
            }

            foreach ($heights[0] as $index => $height) {
                $html = \str_replace(
                    $height,
                    'height:'
                    . ($heightFactor * (int)HelperPreg::removeNonNumericChars($height))
                    . $heights[3][$index]
                    . ';',
                    $html
                );
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
         * @param string $html
         * @param bool $enableTargetBlank
         * @param bool $escapeSingleQuotes
         * @param bool $escapeBackslashes
         * @param bool $disablePurifierCache
         * @param bool $allowVideo
         * @param bool $escapeDoubleQuotes
         * @param array|null $allowedUriSchemes null = HtmlPurifier default config / array = limit to given schemes
         * @return string
         */
        public static function getCleanedHtml(
            string $html,
            bool $enableTargetBlank = false,
            bool $escapeSingleQuotes = false,
            bool $escapeBackslashes = false,
            bool $disablePurifierCache = true,
            bool $allowVideo = false,
            bool $escapeDoubleQuotes = false,
            ?array $allowedUriSchemes = null
        ): string {
            $config = HTMLPurifier_Config::createDefault();

            if ($enableTargetBlank) {
                // Allow target: _blank for open link in new window
                $config->set('Attr.AllowedFrameTargets', ['_blank']);
            }

            if ($disablePurifierCache) {
                // @note    Setting "Core.DefinitionCache" will trigger a PHP error:
                // "Core.DefinitionCache" is an alias for "Cache.DefinitionImpl"
                $config->set('Cache.DefinitionImpl', null);
            }

            if ($allowVideo) {
                // Allow video: only if URL is from YouTube or Vimeo
                $config->set('HTML.SafeIframe', true);

                // Allow fullScreen for videos, with custom HTML purifier filter
                $config->set('Filter.Custom', [new IframeFilter()]);

                $config->set(
                    'URI.SafeIframeRegexp',
                    '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'
                );
            }

            if (null !== $allowedUriSchemes) {
                $config->set('URI.AllowedSchemes', $allowedUriSchemes);
            }

            $html = (new HTMLPurifier($config))->purify($html);

            if ($escapeSingleQuotes) {
                // Escape single quotes to prevent JavaScript error
                $html = \str_replace("'", '&#39;', $html);
            }

            if ($escapeDoubleQuotes) {
                // Escape double quotes to prevent JavaScript error
                $html = \str_replace('"', '&#34;', $html);
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
         * @return string|null
         */
        public static function formatArrayDump(string $dump): ?string
        {
            $dump = \preg_replace('/Array\s*\n\s*/', 'array', $dump);

            if ($dump === null) {
                return null;
            }

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
}  // Gyselroth/Helper
