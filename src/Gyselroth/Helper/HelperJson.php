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

class HelperJson
{
    public const LOG_CATEGORY = 'jsonHelper';

    public const TYPE_OBJECT = 0;
    public const TYPE_ARRAY  = 1;

    /**
     * PHP 7 wrapper for JSON decode: prevent PHP error on empty string
     *
     * @param  string $json
     * @param  int    $objectDecodeType
     * @return array|Object|null
     * @throws \Exception
     */
    public static function decode($json, $objectDecodeType = self::TYPE_ARRAY)
    {
        try {
            /** @noinspection ReturnNullInspection */
            return empty($json) ? null : json_decode($json, self::TYPE_ARRAY === $objectDecodeType);
        } catch (\Exception $e) {
            LoggerWrapper::warning('Cannot decode invalid JSON', [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY, LoggerWrapper::OPT_PARAMS => $json, 'Exception' => $e]);
            return null;
        }
    }

    /**
     * @param  string $str
     * @return string
     */
    public static function ensureIsJson($str): string
    {
        /** @noinspection ReturnFalseInspection */
        return 0 === strpos($str, '<!DOCTYPE') ? 'HTML Code (expected JSON)' : $str;
    }
}
