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

use Gyselroth\Helper\Exception\DateException;
use Gyselroth\Helper\Interfaces\ConstantsUnitsOfTimeInterface;

class HelperDate implements ConstantsUnitsOfTimeInterface
{
    public const LOG_CATEGORY = 'datehelper';

    public const DEFAULT_LOCALE = 'de_CH';

    // Different format identifiers
    public const INDEX_FORMAT_TIMESTAMP_UNIX               = 0;
    public const INDEX_FORMAT_TIMESTAMP_JAVASCRIPT         = 1;
    public const INDEX_FORMAT_DATE_PHP                     = 2;
    public const INDEX_FORMAT_DATETIME_PHP                 = 3;
    public const INDEX_FORMAT_TIME_PHP                     = 4;
    public const INDEX_FORMAT_DATE_ZF1                     = 5;
    public const INDEX_FORMAT_DATETIME_ZF1                 = 6;
    public const INDEX_FORMAT_TIME_ZF1                     = 7;
    public const INDEX_FORMAT_DATETIME_MYSQL               = 8;
    public const INDEX_FORMAT_TIME_MYSQL                   = 9;
    public const INDEX_FORMAT_ZEND_DATE                    = 10;
    public const INDEX_FORMAT_WEEKDAY_SHORT_DAY_MONTH_YEAR = 11;
    public const INDEX_FORMAT_WEEKDAY_LONG_DAY_MONTH_YEAR  = 12;

    // Date shifting modes
    public const SHIFT_MODE_YESTERDAY          = 'yesterday';
    public const SHIFT_MODE_TODAY              = 'today';
    public const SHIFT_MODE_TOMORROW           = 'tomorrow';
    public const SHIFT_MODE_DAY_AFTER_TOMORROW = 'dayAfterTomorrow';
    public const SHIFT_MODE_3_DAYS_LATER       = '3DaysLater';
    public const SHIFT_MODE_4_DAYS_LATER       = '4DaysLater';
    public const SHIFT_MODE_5_DAYS_LATER       = '5DaysLater';

    /**
     * @param  string $str
     * @param  string $format e.g. 'Y-m-d G:i' or 'Y-m-d G:i:s' etc.
     * @return bool
     */
    public static function isDateTimeString($str, $format = 'Y-m-d G:i:s'): bool
    {
        return false !== \DateTime::createFromFormat($format, $str);
    }

    /**
     * @param  string $str              German notation '31.12.2017' / gregorian: '12.31.2017'
     * @param  string $delimiter
     * @param  bool   $isGermanNotation Validate against german notation (dd.mm.yyyy) instead of gregorian (mm.dd.yyyy)
     * @return bool Is date string of format: 3 parts (year, month, day), separated by '-'
     */
    public static function isDateString($str, $delimiter = '-', $isGermanNotation = false): bool
    {
        $parts = \explode($delimiter, $str);

        if (\count($parts) !== 3) {
            return false;
        }

        if (\strlen($parts[2]) > 4) {
            // Might be DateTime format
            return false;
        }
        [$month, $day, $year] = $isGermanNotation
            ? [$parts[1], $parts[0], $parts[2]]
            : [$parts[1], $parts[2], $parts[0]];

        return 3 === \count($parts)
            && \checkdate($month, $day, $year);
    }

    /**
     * @param  string $str
     * @param  int    $digitsInPart1
     * @param  int    $digitsInPart2
     * @param  int    $digitsInPart3
     * @param  string $separator
     * @return bool   Is in date string format, no check for implausible number values though (e.g. day 32, month 15)
     */
    public static function isDateStringFormatted(
        $str,
        $digitsInPart1 = 4,
        $digitsInPart2 = 2,
        $digitsInPart3 = 2,
        $separator = '-'
    ): bool
    {
        return (bool)preg_match(
            '/\d{' . $digitsInPart1 . '}' . $separator
            . '\d{' . $digitsInPart2 . '}' . $separator
            . '\d{' . $digitsInPart3 . '}/',
            $str
        );
    }

    /**
     * @param  string $str
     * @param  bool   $withSeconds
     * @return bool
     */
    public static function isTimeString($str, $withSeconds = true): bool
    {
        return false !== \DateTime::createFromFormat('h:i' . ($withSeconds ? ':s' : ''), $str);
    }

    /**
     * @return string
     * @throws \Zend_Date_Exception
     */
    public static function getCurrentDate(): string
    {
        return (new \Zend_Date())->toString(self::FORMAT_DATE_ZF1_WEEKDAY_LONG_DAY_MONTH_YEAR);
    }

    /**
     * Detect type of given date and return the resp. UNIX timestamp
     *
     * @param  \Zend_Date|Integer|String $date
     * @return int|string|\Zend_Date
     */
    public static function getUnixTimestampFromDate($date)
    {
        if ($date instanceof \Zend_Date) {
            // Return as Zend_Date object
            return $date->toValue();
        }

        return \is_string($date) && !is_numeric($date)
            // Parse date string, return as int
            ? \strtotime($date)
            // Fallback: UNIX timestamp
            : $date;
    }

    /**
     * Render date in given format from given UNIX timestamp
     *
     * @param  int|\Zend_Date $timestamp
     * @param  int            $format
     * @return bool|string
     * @throws \Zend_Date_Exception
     */
    public static function getDateFromUnixTimestamp($timestamp, $format = self::INDEX_FORMAT_TIMESTAMP_UNIX)
    {
        $timestamp = (int)self::getUnixTimestampFromDate($timestamp);

        switch ($format) {
            case self::INDEX_FORMAT_DATE_PHP:
            case self::FORMAT_DATE_PHP:
                /** @noinspection ReturnFalseInspection */
                return \date(self::FORMAT_DATE_PHP, $timestamp);
            case self::INDEX_FORMAT_DATETIME_PHP:
            case self::FORMAT_DATETIME_PHP:
            case self::INDEX_FORMAT_DATETIME_MYSQL:
                /** @noinspection ReturnFalseInspection */
                return \date(self::FORMAT_DATETIME_PHP, $timestamp);
            case self::INDEX_FORMAT_TIME_PHP:
            case self::FORMAT_TIME_PHP:
            case self::INDEX_FORMAT_TIME_MYSQL:
                /** @noinspection ReturnFalseInspection */
                return \date(self::FORMAT_TIME_PHP, $timestamp);
            case self::FORMAT_DATE_ZF1:
            case self::FORMAT_DATETIME_ZF1:
            case self::FORMAT_TIME_ZF1:
                return (new \Zend_Date($timestamp))->toString($format);
            case self::INDEX_FORMAT_TIMESTAMP_JAVASCRIPT:
                return $timestamp * self::MILLISECONDS_SECOND;
            case self::INDEX_FORMAT_ZEND_DATE:
                return new \Zend_Date($timestamp);
            case self::INDEX_FORMAT_WEEKDAY_SHORT_DAY_MONTH_YEAR:
                /** @noinspection ReturnFalseInspection */
                return \date(self::FORMAT_DATE_ZF1_WEEKDAY_SHORT_DAY_MONTH_YEAR, $timestamp);
            case self::INDEX_FORMAT_WEEKDAY_LONG_DAY_MONTH_YEAR:
                $date = new \Zend_Date($timestamp);

                return
                    $date->toString(\Zend_Date::WEEKDAY) . ', '
                  . $date->toString(\Zend_Date::DAY) . '. '
                  . $date->toString(\Zend_Date::MONTH_NAME) . ' '
                  . $date->toString(\Zend_Date::YEAR);
            case self::INDEX_FORMAT_TIMESTAMP_UNIX:
            default:
                // No change
                break;
        }

        return $timestamp;
    }

    /**
     * @param  int $date
     * @return bool|string
     * @throws \Zend_Date_Exception
     */
    public static function getMySqlDateTimeFromDate($date)
    {
        return self::getDateFromUnixTimestamp($date, self::INDEX_FORMAT_DATETIME_MYSQL);
    }

    /**
     * @param string|int $time
     * @return \DateTime
     * @throws \Exception
     */
    public static function getDateTime($time): \DateTime
    {
        return \is_numeric($time)
            ? new \DateTime('@' . (int)$time)
            : new \DateTime($time);
    }

    /**
     * @param  string $str
     * @return string|bool       'yyyy-mm-dd' out of 'yyyy-mm-dd hh:mm:ss'
     */
    public static function getDateStringFromDateTimeString($str)
    {
        if (self::isDateTimeString($str)) {
            return \explode(' ', $str)[0];
        }

        return self::isDateString($str) ? $str : false;
    }

    /**
     * @param  string $dateTimeString
     * @return string  'hh:mm:ss' out of 'yyyy-mm-dd hh:mm:ss'
     */
    public static function getTimeStringFromDateTimeString($dateTimeString): string
    {
        $parts = \explode(' ', $dateTimeString);

        return $parts[1];
    }

    /**
     * @param  int    $timestamp   If non-integer (UNIX timestamp) given: Detect type and parse into UNIX timestamp
     * @param  string $formatYear  Default: 'Y' = 4-digits
     * @param  string $formatMonth Default: 'n' = digit(s) w/o leading 0
     * @param  string $formatDay   Default: 'j' = digitI(s) w/o leading 0
     * @return array
     */
    public static function getDateParts($timestamp, $formatYear = 'Y', $formatMonth = 'n', $formatDay = 'j'): array
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $timestamp = self::getUnixTimestampFromDate($timestamp);

        return [
            self::DATE_TIME_PART_YEAR => \date($formatYear, $timestamp),
            'month'                   => \date($formatMonth, $timestamp),
            self::DATE_TIME_PART_DAY  => \date($formatDay, $timestamp)
        ];
    }

    /**
     * @param  string $date Formats: 'yyyy-mm-dd' or 'year-month-day-hour-min-sec'
     * @return array            Containing date parts (hour, minute, second, month, day, year)
     */
    public static function getDatePartsAtStartOfDay($date): array
    {
        $dateParts = \explode('-', $date);

        return [
            'array'     => $dateParts,
            'timestamp' => \mktime(0, 0, 0, $dateParts[1], $dateParts[2], $dateParts[0])
        ];
    }

    /**
     * @param  int|string $timestamp UNIX timestamp or date like 'yyyy-mm-dd'
     * @return int          Timestamp   UNIX timestamp of start of day (00:00:00)
     */
    public static function getTimestampStartOfDay($timestamp): int
    {
        $dateParts = self::getDateParts($timestamp);

        return \mktime(0, 0, 0, $dateParts['month'], $dateParts[self::DATE_TIME_PART_DAY], $dateParts[self::DATE_TIME_PART_YEAR]);
    }

    /**
     * @param  int $timestamp
     * @return int              Timestamp for end (23:59:59) of day
     */
    public static function getTimestampEndOfDay($timestamp): int
    {
        $dateParts = self::getDateParts($timestamp);

        return \mktime(23, 59, 59, $dateParts['month'], $dateParts[self::DATE_TIME_PART_DAY], $dateParts[self::DATE_TIME_PART_YEAR]);
    }

    /**
     * @param  int|string $timeStr   Time string tupel like: hh:mm, or triplet like: hh:mm:ss, accepts also a UNIX timestamp
     * @param  String     $delimiter Default: ':'
     * @param  bool       $includeMinutes
     * @param  bool       $includeSeconds
     * @return array                Time parts as integers, item keys: 'hour', 'minutes', if given in $timeStr: 'seconds'
     */
    public static function getTimeStringParts($timeStr, $delimiter = ':', $includeMinutes = true, $includeSeconds = true): array
    {
        if (\is_numeric($timeStr)) {
            $timeStr = self::getTimeString($timeStr);
        } elseif (empty($timeStr) || false === \strpos($timeStr, $delimiter)) {
            // Unsupported timeString format detected
            // @todo log: 'Illegal hour time string parsed in ' . __CLASS__
            exit;
        }

        $timeStringParts = \explode($delimiter, $timeStr);

        $parts = [
            self::DATE_TIME_PART_HOUR => (int)$timeStringParts[0],
            'minutes'                 => $includeMinutes ? (int)$timeStringParts[1] : 0
        ];

        if (\count($timeStringParts) > 2) {
            $parts['seconds'] = $includeSeconds ? (int)$timeStringParts[2] : 0;
        }

        return $parts;
    }

    /**
     * @param  int|string $timestamp
     * @param  bool       $isMilliSeconds
     * @param  bool       $includeSeconds
     * @param  bool       $isCurrentDate
     * @return string          Time of day of given $timestamp, formatted like 'hh:mm:ss'
     */
    public static function getTimeString($timestamp, $isMilliSeconds = false, $includeSeconds = true, $isCurrentDate = false): string
    {
        if (\is_string($timestamp)
            && !\is_numeric($timestamp)
        ) {
            // Convert time string like '14:00' or '14:00:00' to sum of seconds
            $timestamp      = self::getSumSecondsOfTimeString($timestamp, true, true, $isCurrentDate);
            $isMilliSeconds = false;
        }

        return \date(
            $includeSeconds ? self::FORMAT_TIME_PHP : self::FORMAT_TIME_NO_SECONDS_PHP,
            $isMilliSeconds ? ($timestamp / self::MILLISECONDS_SECOND) : $timestamp
        );
    }

    /**
     * @param  array $timeParts as created from HelperDate::getTimeStringParts()
     * @param  bool  $isCurrentDate
     * @return int   Sum of seconds of given time parts array: hour, minutes and seconds (if given)
     */
    public static function getSumSecondsOfTimeParts(array $timeParts, $isCurrentDate = false): int
    {
        return $isCurrentDate
            ? \mktime(
                $timeParts[self::DATE_TIME_PART_HOUR],
                $timeParts['minutes'],
                \array_key_exists('seconds', $timeParts) ? $timeParts['seconds'] : 0)
            :
            $timeParts[self::DATE_TIME_PART_HOUR]
            * self::SECONDS_HOUR + $timeParts['minutes']
            * self::SECONDS_MIN
            + (array_key_exists('seconds', $timeParts)
                ? $timeParts['seconds']
                : 0);
    }

    /**
     * @param  array      $timeParts
     * @param  bool|false $isCurrentDate
     * @return float
     */
    public static function getSumMinutesOfTimeParts(array $timeParts, $isCurrentDate = false): float
    {
        return self::getSumSecondsOfTimeParts($timeParts, $isCurrentDate) / self::SECONDS_MIN;
    }

    /**
     * @param  string $timeStr $timeStr    i.e. '07:30' or '09:45:00'
     * @param  bool   $includeMinutes
     * @param  bool   $includeSeconds
     * @param  bool   $isCurrentDate
     * @return int
     */
    public static function getSumSecondsOfTimeString(
        $timeStr,
        bool $includeMinutes = true,
        bool $includeSeconds = true,
        bool $isCurrentDate = false): int
    {
        return self::getSumSecondsOfTimeParts(
            self::getTimeStringParts($timeStr, ':', $includeMinutes, $includeSeconds),
            $isCurrentDate);
    }

    /**
     * Get sum of minutes of given time string
     *
     * @param  string $time e.g. '08:30:00' or '08:30'
     * @return int              e.g. 510
     */
    public static function getSumMinutesOfTimeString($time): int
    {
        $parts = self::getTimeStringParts($time);

        return $parts['minutes'] + $parts[self::DATE_TIME_PART_HOUR] * self::SECONDS_MIN;
    }

    /**
     * If timestamp of given date is within a weekend: increment timestamp until at 1st day of next week
     *
     * @param  \Zend_Date $date
     * @param  bool      $showSaturday
     * @param  bool      $showSunday
     * @return \Zend_Date
     * @throws \Zend_Date_Exception
     */
    public static function skipWeekend($date, $showSaturday, $showSunday): \Zend_Date
    {
        $weekDigit = $date->toValue(\Zend_Date::WEEKDAY_DIGIT);

        if (
            (0 === $weekDigit || 6 === $weekDigit)
            && (!$showSaturday || !$showSunday)
        ) {
            // Sunday
            if (0 === $weekDigit && !$showSunday) {
                $date->addDay(1);
                // Saturday
            } elseif (6 === $weekDigit && !$showSaturday) {
                $date->addDay($showSunday ? 1 : 2);
            }
        }

        return $date;
    }

    /**
     * @param  int  $timestamp
     * @param  bool $isMilliSeconds Is given timestamp milli seconds format? Default: false (= is UNIX timestamp)
     * @return int  Day of week (0-6, 1=MON)
     */
    public static function getWeekdayNumberFromTimestamp($timestamp, $isMilliSeconds = false): int
    {
        if ($isMilliSeconds) {
            $timestamp /= self::MILLISECONDS_SECOND;
        }

        $dayNum = (int)\date('N', $timestamp);

        return 7 === $dayNum ? 0 : $dayNum;
    }

    /**
     * Calculates the monday of the week for a given date
     *
     * @param  \Zend_Date|String|null $date
     * @return \Zend_Date
     * @throws \InvalidArgumentException
     * @throws \Zend_Date_Exception
     */
    public static function getMondayOfWeek($date = null): \Zend_Date
    {
        $date = empty($date) ? new \Zend_Date() : $date;

        if (\is_string($date)) {
            // Convert to Zend_Date
            $date = empty($date) ? new \Zend_Date() : new \Zend_Date($date);
        } elseif (!\is_object($date) || 'Zend_Date' !== \get_class($date)) {
            throw new \InvalidArgumentException(
                'Argument 1 passed to ' . __CLASS__ . '::' . __METHOD__ . ' must be an instance of Zend_Date or null or string');
        }

        $monday = empty($date) ? new \Zend_Date() : clone $date;
        $monday->set('00:00:00', \Zend_Date::TIMES);
        $dayOfWeek = (int)$monday->get(\Zend_Date::WEEKDAY_DIGIT);

        // $dayOfWeek = 0 -> Sunday
        return $monday->sub(0 === $dayOfWeek ? 6 : ($dayOfWeek - 1), \Zend_Date::DAY_SHORT);
    }

    /**
     * @param  \Zend_Date $date1
     * @param  \Zend_Date $date2
     * @param  string     $unit Currently this can only be 'day'
     * @return int|null             Difference in given unit, no return value if unit unknown
     * @throws \InvalidArgumentException
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Exception
     * @throws \Zend_Date_Exception
     */
    public static function getDateDiff(\Zend_Date $date1, \Zend_Date $date2, $unit = 'day'): ?int
    {
        switch ($unit) {
            case self::DATE_TIME_PART_DAY:
                // Set the same time on both days
                $date1->set('00:00:00', \Zend_Date::TIMES);
                $date2->set('00:00:00', \Zend_Date::TIMES);

                return (int)($date2->sub($date1)->toValue() / self::SECONDS_DAY) + 1;
            case self::DATE_TIME_PART_HOUR:
                return (int)($date2->sub($date1)->toValue() / self::SECONDS_HOUR);
            default:
                LoggerWrapper::warning(
                    "Detected unhandled unit $unit",
                    [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY, LoggerWrapper::OPT_PARAMS => $unit]);

                return null;
        }
    }

    /**
     * Get array of weeks in time range (keys: 'startWeek', 'weeks', 'endWeek')
     *
     * @param  int  $dateFrom UNIX timestamp
     * @param  int  $dateTo   UNIX timestamp
     * @return array|bool
     */
    public static function getWeeksBetween($dateFrom, $dateTo)
    {
        $weeks              = [];
        $dayOfWeek          = \date('w', $dateFrom);
        $fromWeekStart      = $dateFrom - ($dayOfWeek * self::SECONDS_DAY) - ($dateFrom % self::SECONDS_DAY);
        $weeks['startWeek'] = \date('W', $fromWeekStart);
        $diffDays           = self::getDaysBetween($dateFrom, $dateTo);
        $diffWeeks          = (int)($diffDays / 7);
        $secondsLeft        = ($diffDays % 7) * self::SECONDS_DAY;

        if (($dateFrom - $fromWeekStart) + $secondsLeft > self::SECONDS_WEEK) {
            $diffWeeks++;
        }

        $startWeek = \date('W', $fromWeekStart);

        return [
            'startWeek' => $startWeek,
            'weeks'     => $diffWeeks,
            'endWeek'   => $startWeek + $diffWeeks
        ];
    }

    /**
     * @param  int $dateFrom
     * @param  int $dateTo
     * @return int
     */
    public static function getDaysBetween($dateFrom, $dateTo): int
    {
        $fromDayStart = \mktime(0, 0, 0, \date('m', $dateFrom), \date('d', $dateFrom), \date('Y', $dateFrom));
        $diff         = $dateTo - $dateFrom;
        $days         = (int)($diff / self::SECONDS_DAY);

        return ($dateFrom - $fromDayStart) + ($diff % self::SECONDS_DAY) > self::SECONDS_DAY ? $days + 1 : $days;
    }

    /**
     * Get month name according to number (1-12) translated
     *
     * @param  int  $month (1-12)   if > 12: assumed being a timestamp and converted to the resp. month number
     * @param  bool $abbreviated
     * @return string
     */
    public static function getMonthNameByNumber($month, $abbreviated = false): string
    {
        if ($month > 12) {
            // Assumed being a timestamp and converted to the resp. month number
            $month = \date('n', $month);
        }

        $months = [
            1  => HelperString::translate('Januar'),
            2  => HelperString::translate('Februar'),
            3  => HelperString::translate('März'),
            4  => HelperString::translate('April'),
            5  => HelperString::translate('Mai'),
            6  => HelperString::translate('Juni'),
            7  => HelperString::translate('Juli'),
            8  => HelperString::translate('August'),
            9  => HelperString::translate('September'),
            10 => HelperString::translate('Oktober'),
            11 => HelperString::translate('November'),
            12 => HelperString::translate('Dezember')
        ];

        return $abbreviated
            ? \mb_substr($months[$month], 0, 3)
            : $months[$month];
    }

    /**
     * Get weekday name according to number (1-7) translated
     *
     * @param  int  $day (1-7)      if > 7: assumed being a timestamp and converted to the weekday number
     * @param  bool $abbreviated
     * @return string
     */
    public static function getWeekdayNameByNumber($day, $abbreviated = false): string
    {
        if ($day > 7) {
            // Assumed being a timestamp and converted to the resp. weekday number
            $day = \date('n', $day);
        }

        if ($abbreviated) {
            $daysAbbreviated = [
                1 => HelperString::translate('Mo'),
                2 => HelperString::translate('Di'),
                3 => HelperString::translate('Mi'),
                4 => HelperString::translate('Do'),
                5 => HelperString::translate('Fr'),
                6 => HelperString::translate('Sa'),
                7 => HelperString::translate('So')
            ];

            return $daysAbbreviated[$day];
        }

        $days = [
            1 => HelperString::translate('Montag'),
            2 => HelperString::translate('Dienstag'),
            3 => HelperString::translate('Mittwoch'),
            4 => HelperString::translate('Donnerstag'),
            5 => HelperString::translate('Freitag'),
            6 => HelperString::translate('Samstag'),
            7 => HelperString::translate('Sonntag')
        ];

        return $days[$day];
    }

    /**
     * Converts the given string to a date according to the ICS standard
     *
     * @param  string $dateStr
     * @param  bool   $appendTime
     * @return string
     * @throws \Gyselroth\Helper\Exception\DateException
     */
    public static function getIcsDateFromDateString($dateStr, $appendTime = false): string
    {
        $timestamp = \strtotime($dateStr);
        if (false === $timestamp) {
            throw new DateException("Failed converting '$dateStr' to a date value.");
        }

        return
            \date('Ymd', $timestamp)
          . ($appendTime
                ? 'T' . \date('His', $timestamp)
                : '');
    }

    /**
     * @param  string $dateStr
     * @return string
     * @throws \Gyselroth\Helper\Exception\DateException
     */
    public static function getIcsDateTimeFromDateString($dateStr): string
    {
        return self::getIcsDateFromDateString($dateStr, true);
    }

    /**
     * @param  int $weekNumber Week number of year (0 - 52)
     * @param  int $year       2- or 4-digit year number
     * @return int
     */
    public static function getTimestampFirstDayOfCalendarWeek($weekNumber, $year): int
    {
        if ($year < 100) {
            $year += 2000;
        }

        return \strtotime($year . 'W' . HelperNumeric::formatAmountDigits($weekNumber, 2));
    }

    /**
     * @param  string|int $year 4-digit year
     * @return bool|string
     */
    public static function getAgeByBirthYear($year)
    {
        return \date('Y') - $year;
    }

    /**
     * @param  int   $searchDate Timestamp
     * @param  array $dates
     * @return int|null
     */
    public static function getClosestDate($searchDate, $dates): ?int
    {
        $closest = null;
        if ($dates) {
            foreach ($dates as $item) {
                if (empty($closest)
                    || \abs($searchDate - $closest) > \abs($item - $searchDate)) {
                    $closest = $item;
                }
            }
        }

        return $closest;
    }

    /**
     * @param  \Zend_Date $date
     * @param  string     $shiftingMode
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Exception
     * @throws \Zend_Date_Exception
     */
    public static function getDateShifted($date, $shiftingMode = 'today'): array
    {
        $amountDaysAdded  = 0;
        $tempDate         = clone $date;
        $hasPassedWeekend = false;

        switch ($shiftingMode) {
            case self::SHIFT_MODE_YESTERDAY:
                $date->subDay(1);
                break;
            case self::SHIFT_MODE_TODAY:
                break;
            case self::SHIFT_MODE_TOMORROW:
                $date->addDay(1);
                break;
            case self::SHIFT_MODE_DAY_AFTER_TOMORROW:
                $date->addDay(2);
                $amountDaysAdded = 2;
                break;
            case self::SHIFT_MODE_3_DAYS_LATER:
                $date->addDay(3);
                $amountDaysAdded = 3;
                break;
            case self::SHIFT_MODE_4_DAYS_LATER:
                $date->addDay(4);
                $amountDaysAdded = 4;
                break;
            case self::SHIFT_MODE_5_DAYS_LATER:
                $date->addDay(5);
                $amountDaysAdded = 5;
                break;
            default:
                LoggerWrapper::warning(
                    __CLASS__ . '::' . __FUNCTION__ . " - Unknown shifting mode $shiftingMode",
                    [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY, LoggerWrapper::OPT_PARAMS => $shiftingMode]);
                break;
        }

        if ($amountDaysAdded > 0) {
            for ($addDayIndex = 0; $addDayIndex < $amountDaysAdded; $addDayIndex++) {
                $tempDate->addDay(1);
                $weekdayIndex = $tempDate->toValue(\Zend_Date::WEEKDAY_DIGIT);
                if (6 === $weekdayIndex || 0 === $weekdayIndex) {
                    $hasPassedWeekend = true;
                    break;
                }
            }
        }

        return [$tempDate, $hasPassedWeekend];
    }

    /**
     * @param  string|int $dateStart Starting date as dateTime string or UNIX timestamp
     * @param  string|int $dateEnd   Ending date as dateTime string or UNIX timestamp
     * @param  string     $locale
     * @return string         Human readable timerange, in locale-aware format
     * @throws \Zend_Date_Exception
     * @todo: "bis" needs to be translated as well if locale is set to "en"
     */
    public static function renderTimerangeHumanReadable($dateStart, $dateEnd, $locale = null): string
    {
        if (null === $locale) {
            $locale = self::DEFAULT_LOCALE;
        }

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $dateStart = new \Zend_Date(self::getDateFromUnixTimestamp($dateStart), \Zend_Date::ISO_8601, $locale);
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $dateEnd   = new \Zend_Date(self::getDateFromUnixTimestamp($dateEnd), \Zend_Date::ISO_8601, $locale);

//        return $dateStart->get(\Zend_Date::DATE_LONG) . ' ' . Zend_Registry::get('Zend_Translate')->translate('bis') . ' ' . $dateEnd->get(Zend_Date::DATE_LONG);
        return $dateStart->get(\Zend_Date::DATE_LONG) . ' ' . 'bis' . ' ' . $dateEnd->get(\Zend_Date::DATE_LONG);
    }

    /**
     * @param  string $dateStr
     * @return string Given date string w/o "am" or "pm"
     */
    public static function removeMeridiem($dateStr): string
    {
        return \trim(
            str_replace(
                ['am', 'pm'],
                ['', ''],
                \strtolower($dateStr))
        );
    }

    /**
     * @param  string $timeString
     * @return string
     */
    public static function ensureTimeStringHasSeconds($timeString): string
    {
        return $timeString
            . (1 === \substr_count($timeString, ':') ? ':00' : '');
    }

    /**
     * @param  string|int $date Date, DateTime or Timestamp
     * @param  string     $format
     * @return string
     * @throws \Zend_Locale_Exception
     * @throws \Zend_Date_Exception
     */
    public static function convertDateToUTC($date, $format = self::FORMAT_UTC_ZF1): string
    {
        $utcDate = new \Zend_Date($date, null, new \Zend_Locale('de_CH'));
        $utcDate->setTimezone('UTC');

        return $utcDate->toString($format);
    }

    /**
     * @param  string $date
     * @param  string $delimiter
     * @return array|string
     */
    public static function convertDelimitedDateString($date, $delimiter = '-')
    {
        $dateParts = \explode($delimiter, $date);

        return $dateParts[2] . '.' . $dateParts[1] . '.' . $dateParts[0];
    }

    /**
     * @param  string $type
     * @return string
     */
    public static function getZendDatePartByType($type): ?string
    {
        $DATE_TIME_PART_MONTH = 'month';
        switch (\strtolower($type)) {
            case self::DATE_TIME_PART_SECOND:
                return \Zend_Date::SECOND;
            case self::DATE_TIME_PART_MINUTE:
                return \Zend_Date::MINUTE;
            case self::DATE_TIME_PART_HOUR:
                return \Zend_Date::HOUR;
            case self::DATE_TIME_PART_DAY:
                return \Zend_Date::DAY;
            case self::DATE_TIME_PART_WEEK:
                return \Zend_Date::WEEK;
            case $DATE_TIME_PART_MONTH:
                return \Zend_Date::MONTH;
            case self::DATE_TIME_PART_YEAR:
                return \Zend_Date::YEAR;
            default:
                return \Zend_Date::TIMESTAMP;
        }
    }

    /**
     * @param  string $dateStr
     * @return string           E.g. "12:30" out of "12:30 2017.12.31"
     */
    public static function getTimeOutOfTimeDateString($dateStr): string
    {
        return \substr($dateStr, 0, 5);
    }

    /**
     * @return bool|string
     */
    public static function getCurrentWeekAndYear()
    {
        return \date('W / Y');
    }

    public static function getDayNumberOfWeekFromZendDate(\Zend_Date $date): int
    {
        $dateValue = $date->toValue(\Zend_Date::WEEKDAY_DIGIT);

        return 0 === $dateValue ? 7 : $dateValue;
    }

    /**
     * @param Zend_Date $date
     * @param int $addDays
     * @return bool
     * @throws Zend_Date_Exception
     * @throws \Zend_Date_Exception
     */
    public static function passesWeekend(\Zend_Date $date, int $addDays): bool
    {
        $tempDate = clone $date;
        for ($count = 0; $count < $addDays; $count++) {
            $tempDate->addDay(1);
            $weekDayNumber = $tempDate->toValue(\Zend_Date::WEEKDAY_DIGIT);
            if ($weekDayNumber === 6 || $weekDayNumber === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $start1 e.g. '07:45'
     * @param string $end1   e.g. '08:30'
     * @param string $start2 e.g. '07:45'
     * @param string $end2   e.g. '08:30'
     * @return bool
     */
    public static function timeSpansIntersect(string $start1, string $end1, string $start2, string $end2): bool
    {
        return \date($start1) >= \date($start2)
            && \date($end1) <= \date($end2);
    }

    /**
     * @param string $timeSpan E.g.: '07:45 - 08:30'
     * @return bool
     */
    public static function isTimeSpan(string $timeSpan): bool
    {
        \preg_match('/[\d]+:[\d]+ - [\d]+:[\d]+/', $timeSpan, $matches);

        return isset($matches[0]);
    }
}
