<?php

/**
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Gyselroth\Helper\Interfaces;

interface ConstantsUnitsOfTimeInterface
{
    public const DATE_TIME_PART_DAY    = 'day';
    public const DATE_TIME_PART_HOUR   = 'hour';
    public const DATE_TIME_PART_MINUTE = 'minute';
    public const DATE_TIME_PART_SECOND = 'second';
    public const DATE_TIME_PART_WEEK   = 'week';
    public const DATE_TIME_PART_YEAR   = 'year';

    // Seconds per minute / hour / day / week
    public const SECONDS_MIN  = 60;
    public const SECONDS_HOUR = 3600;
    public const SECONDS_DAY  = 86400;

    // Seconds/week includes weekend (7 days)
    public const SECONDS_WEEK = 604800;
    public const MINUTES_DAY  = 1440;
    public const HOURS_DAY    = 24;

    public const MILLISECONDS_SECOND = 1000;

    // Date and datetime formats for PHP date() parameter
    public const FORMAT_DATE_PHP            = 'Y-m-d';
    public const FORMAT_DATETIME_PHP        = 'Y-m-d H:i:s';
    public const FORMAT_TIME_PHP            = 'H:i:s';
    public const FORMAT_TIME_NO_SECONDS_PHP = 'H:i';

    // Date and datetime formats for Zend_Date
    public const FORMAT_DATE_ZF1                              = 'yyyy-MM-dd';
    public const FORMAT_DATE_ZF1_WEEKDAY_SHORT_DAY_MONTH_YEAR = 'D, j. F Y';
    public const FORMAT_DATE_ZF1_WEEKDAY_LONG_DAY_MONTH_YEAR  = 'EEEE, dd. MMMM y';
    public const FORMAT_DATETIME_ZF1                          = 'yyyy-MM-dd HH:mm:ss';
    public const FORMAT_TIME_ZF1                              = 'HH:mm:ss';
    public const FORMAT_TIME_WITHOUT_SECONDS_ZF1              = 'HH:mm';

    // FORMAT_DATE_DIN is DIN 5008
    public const FORMAT_DATE_DIN = 'dd.MM.Y';
    public const FORMAT_UTC_ZF1  = 'yyyyMMddTHHmmss';
}
