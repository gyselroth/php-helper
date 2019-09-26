<?php

/**
 * Intranet Sek II
 * Copyright (c) 2012-2018 gyselroth™ (http://www.gyselroth.net)
 */

namespace Gyselroth\Helper;

use HTMLPurifier_Context;
use HTMLPurifier_Config;

/**
 * HTML Purifier - custom html purifier filter for allowing fullscreen of iframes(videos) with src "youtube" or "vimeo"
 */
class HelperHtmlPurifierIframeFilter
{
    /** @var string */
    public $name = 'IframeAllowFullscreen';

    /**
     * @param  string                $html
     * @param  HTMLPurifier_Config   $config
     * @param  HTMLPurifier_Context  $context
     * @return string
     * @todo review: arguments $config and $context are unused
     */
    public function preFilter($html, $config, $context)
    {
        $html = \preg_replace('#<iframe#i', '<img class="video-iframe-allow-fullscreen"', $html);

        return \preg_replace('#</iframe>#i', '</img>', $html);
    }

    /**
     *
     * @param  string $html
     * @param  HTMLPurifier_Config $config
     * @param  HTMLPurifier_Context $context
     * @return string
     * @todo review: arguments $config and $context are unused
     */
    public function postFilter(string $html, $config, $context)
    {
        $pattern = '#<img class="video-iframe-allow-fullscreen"([^>]+?)>#';

        return \preg_replace_callback($pattern, [$this, 'postFilterCallback'], $html);
    }

    /**
     * Filter matches[1] against domain whitelist
     *
     * @param  array $matches
     * @return string
     */
    protected function postFilterCallback($matches): string
    {
        return \preg_match('#src="https?://www.youtube(-nocookie)?.com/#i', $matches[1])
            || \preg_match('#src="http://player.vimeo.com/#i', $matches[1])
                ? '<iframe ' . $matches[1] . ' frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>'
                : '';
    }
}
