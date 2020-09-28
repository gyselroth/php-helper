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
 * HTML Purifier - custom html purifier filter for allowing fullscreen of iframes(videos) with src "youtube" or "vimeo"
 */
class HelperHtmlPurifierIframeFilter
{

    public string $name = 'IframeAllowFullscreen';

    /**
     * @param  string                $html
     * @return string|string[]|null
     */
    public function preFilter($html)
    {
        $html = \preg_replace('#<iframe#i', '<img class="video-iframe-allow-fullscreen"', $html);

        if ($html === null) {
            return null;
        }

        return \preg_replace('#</iframe>#i', '</img>', $html);
    }

    /**
     * @param  string $html
     * @return string|string[]|null
     */
    public function postFilter(string $html)
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
                ? '<iframe '
                  . $matches[1]
                  . ' frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>'
                : '';
    }
}
