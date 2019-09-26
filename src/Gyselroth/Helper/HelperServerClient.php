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

use Gyselroth\Helper\Interfaces\ConstantsHttpInterface;

/**
 * Server/Client helpers: Environment settings, MVC, AJAX
 */
class HelperServerClient implements ConstantsHttpInterface
{
    private const LOG_CATEGORY_REQUEST = 'request';

    protected const PATTERN_USER_AGENTS_MOBILE_DEVICES =
        '/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|'
        . 'htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|'
        . 'panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus|'
        . 'up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i';

    public static function getHost(bool $withProtocol = true): string
    {
        $protocol = '';
        if ($withProtocol) {
            $protocol = 'http' .
                (isset($_SERVER['HTTPS']) && 'off' !== $_SERVER['HTTPS']
                    ? 's'
                    : ''
                ) . '://';
        }

        return $protocol . $_SERVER['HTTP_HOST'];
    }

    /**
     * @param  array|int|string|\Zend_Date $requestTime
     * @param  \Zend_Date                  $modificationTime
     * @return string
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \Zend_Date_Exception
     */
    public static function getDiffOfRequestAndLocalTime($requestTime, $modificationTime): string
    {
        $now = new \Zend_Date();
        // Diff: 0 = equal, 1 = later, -1 = earlier
        $diff = $now->compare($requestTime);
        switch ($diff) {
            // Requesting servers timestamp is earlier than timestamp of this machine
            case 1:
                $diff = $now->get(\Zend_Date::TIMESTAMP) - $requestTime->get(\Zend_Date::TIMESTAMP);
                $modificationTime->sub($diff, \Zend_Date::TIMESTAMP);
                break;
            // Requesting servers timestamp is later than timestamp of this machine
            case -1:
                $diff = $requestTime->get(\Zend_Date::TIMESTAMP) - $now->get(\Zend_Date::TIMESTAMP);
                $modificationTime->add($diff, \Zend_Date::TIMESTAMP);
                break;
            default:
                LoggerWrapper::warning("Detected unhandled time diff value: $diff", [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY_REQUEST]);
                break;
        }

        return $modificationTime->toString('yyyy-MM-dd HH:mm:ss');
    }

    /**
     * @param  bool $namesOnly
     * @param  bool $associative
     * @return array Names of fonts installed on server
     * @note   tested and working on Ubuntu 14.04
     */
    public static function getInstalledFonts(bool $namesOnly = false, bool $associative = false): array
    {
        if ($namesOnly) {
            $associative = true;
        }

        $fontLines = \explode("\n", \shell_exec('fc-list'));

        $fonts = [];
        $index = 0;
        foreach ($fontLines as $fontLine) {
            $fontName                                 = HelperString::getStringBetween($fontLine, ': ', ':style=');
            $fonts[$associative ? $fontName : $index] = [
                'path'  => HelperString::removeAllAfter(':', $fontLine, 1, true),
                'name'  => $fontName,
                'style' => HelperString::removeAllBefore(':style=', $fontLine, 0, true)
            ];
            $index++;
        }

        if ($namesOnly) {
            $fonts = \array_keys($fonts);
            \sort($fonts);
        } else {
            \ksort($fonts);
        }

        return $fonts;
    }

    public static function getRequestUrl(): string
    {
        return 'http'
            . (\array_key_exists('HTTPS', $_SERVER)
            && 'on' === $_SERVER['HTTPS']
                ? 's'
                : ''
            )
            . '://'
            . $_SERVER['SERVER_NAME']
            . (80 !== $_SERVER['SERVER_PORT']
                ? ':' . $_SERVER['SERVER_PORT']
                : ''
            ) . $_SERVER['REQUEST_URI'];
    }

    public static function getClientIP(): string
    {
        if (\array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $forwardedForItems = \explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            if (!empty($forwardedForItems)) {
                /** @noinspection ReturnNullInspection */
                return \array_pop($forwardedForItems);
            }
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    public static function isClientWindows(): bool
    {
        /** @noinspection ReturnFalseInspection */
        return false !== \strpos($_SERVER['HTTP_USER_AGENT'], 'Win');
    }

    /**
     * Removes unnecessary data from request array to use it for response
     *
     * @param  array $data request data array to prepare for response
     * @param  array  $indexesToUnset
     * @return array
     * @todo make $indexesToUnset general applicable instead of IN2 specific
     */
    public static function prepareAjaxResponseData(
        array $data,
        array $indexesToUnset = ['model', 'controller', 'action', 'school']
    ): array
    {
        if (empty($data)) {
            return $data;
        }

        foreach ($indexesToUnset as $index) {
            if (isset($data[$index])) {
                unset($data[$index]);
            }
        }

        return $data;
    }

    /**
     * Extract parts of given URI (returns empty array if not a valid URI)
     *
     * @param  string $str
     * @return array            URI parts: schema, user, password, host, [port,] path
     */
    public static function getUriParts($str): array
    {
        $pattern = '/^(.*):\/\/(.*):(.*)@(.*):(.*)/';
        \preg_match($pattern, $str, $matches);

        return $matches;
    }

    public static function isMobileDevice(): bool
    {
        return \preg_match(
            self::PATTERN_USER_AGENTS_MOBILE_DEVICES,
            $_SERVER['HTTP_USER_AGENT']);
    }
}
