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

class HelperTimerange
{
    public const DEFAULT_LOCALE = 'de_CH';

    /**
     * @param string $timeSpan E.g.: '07:45 - 08:30'
     * @return bool
     */
    public static function isTimeSpan(string $timeSpan): bool
    {
        \preg_match('/[\d]+:[\d]+ - [\d]+:[\d]+/', $timeSpan, $matches);

        return isset($matches[0]);
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
     * @param  string|int  $dateStart Starting date as dateTime string or UNIX timestamp
     * @param  string|int  $dateEnd   Ending date as dateTime string or UNIX timestamp
     * @param  string|null $locale
     * @return string      Human readable timerange, in locale-aware format
     * @throws \Zend_Date_Exception
     */
    public static function renderTimerangeHumanReadable($dateStart, $dateEnd, ?string $locale = null): string
    {
        if (null === $locale) {
            $locale = self::DEFAULT_LOCALE;
        }

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $dateStart = new \Zend_Date(
            HelperDate::getDateFromUnixTimestamp((int)$dateStart)?: null,
            \Zend_Date::ISO_8601,
            $locale
        );

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $dateEnd = new \Zend_Date(
            HelperDate::getDateFromUnixTimestamp((int)$dateEnd)?: null,
            \Zend_Date::ISO_8601,
            $locale
        );

        return $dateStart->get(\Zend_Date::DATE_LONG) 
            . ' ' . HelperString::translate('bis') . ' '
            . $dateEnd->get(\Zend_Date::DATE_LONG);
    }

    /**
     * @param  string $date      Date in format compatible w/ strtotime(), e.g. 'yyyy-mm-dd'
     * @param  string $startDate Date in format compatible w/ strtotime(), e.g. 'yyyy-mm-dd'
     * @param  string $endDate   Date in format compatible w/ strtotime(), e.g. 'yyyy-mm-dd'
     * @return bool
     */
    public static function isDateStringInRange(string $date, string $startDate, string $endDate): bool
    {
        $timestamp = \strtotime($date);

        return $timestamp >= \strtotime($startDate)
            && $timestamp <= \strtotime($endDate);
    }

    /**
     * Detect whether to time ranges overlap
     *
     * @todo add summertime handling
     *       e.g. Application_Helper_Timerange::doRangesOverlap(
     *              '1.1.2017','25.4.2018 02:00','25.4.2018 03:00','4.5.2018',false
     *            ) should not overlap.
     *
     * @param  \Zend_Date|int|string $startRange1
     * @param  \Zend_Date|int|string $endRange1
     * @param  \Zend_Date|int|string $startRange2
     * @param  \Zend_Date|int|string $endRange2
     * @param  bool                  $allowTouching Should an overlap of 1 sec be seen as collision or be allowed?
     *                                              (eg. lesson 1 ends at 16:00 and lesson 2 starts at 16:00)
     * @return bool
     */
    public static function doRangesOverlap(
        $startRange1,
        $endRange1,
        $startRange2,
        $endRange2,
        bool $allowTouching = true
    ): bool
    {
        // Ensure all times are UNIX timestamps
        $startRange1 = HelperDate::getUnixTimestampFromDate($startRange1);
        $endRange1   = HelperDate::getUnixTimestampFromDate($endRange1);

        $startRange2 = HelperDate::getUnixTimestampFromDate($startRange2);
        $endRange2   = HelperDate::getUnixTimestampFromDate($endRange2);

        if ($allowTouching) {
            $endRange1--;
            $endRange2--;
        }

        // Check: range1 starts before range2 ends, and range2 ends after range1 starts?
        return $startRange1 <= $endRange2
            && $endRange1 > $startRange2;
    }

    /**
     * @param int $timestampStart
     * @param int $timestampEnd
     * @param int $timestampStartAllowed
     * @param int $timestampEndAllowed
     * @return bool
     */
    public static function rangeStartsBeforeAndEndsInAllowedRange(
        int $timestampStart,
        int $timestampEnd,
        int $timestampStartAllowed,
        int $timestampEndAllowed
    ): bool {
        return $timestampStart < $timestampStartAllowed
            && $timestampEnd > $timestampStartAllowed
            && $timestampEnd <= $timestampEndAllowed;
    }

    /**
     * @param int $timestampStart
     * @param int $timestampEnd
     * @param int $timestampStartAllowed
     * @param int $timestampEndAllowed
     * @return bool
     */
    public static function rangeStartsInAndEndsAfterAllowedRange(
        int $timestampStart,
        int $timestampEnd,
        int $timestampStartAllowed,
        int $timestampEndAllowed
    ): bool {
        return $timestampEnd > $timestampEndAllowed
            && $timestampStart >= $timestampStartAllowed
            && $timestampStart < $timestampEndAllowed;
    }

    /**
     * Find maximum amount of overlaps which the given range has with the given compare-ranges
     *
     * @note this method assumes the given ranges to be within the same day
     *
     * The method uses a trick (instead of implementing a "interval tree" structure):
     * it generates and scans an image, consisting of a line per range: representing the minutes in a day
     * which this range spans
     *
     * @param  array $range         Range array, like: [0 => start, 1 => end]
     * @param  array $rangesCompare Array of range arrays
     * @return int
     */
    public static function getMaximumSimultaneousOverlaps($range, $rangesCompare): int
    {
        $amountRanges = \count($rangesCompare) + 1;

        // Setup white image, sized: 1440 x (amount of ranges) pixel
        // Must be 1 ultimately, for more obvious debug review of image, set to 2
        $stepSize = 1;
        $image    = \imagecreate(HelperDate::MINUTES_DAY, $amountRanges * $stepSize + 2);

        if (false === $image) {
            return 0;
        }

        $black = \imagecolorallocate($image, 0, 0, 0);
        $white = \imagecolorallocate($image, 255, 255, 255);

        \imagefill($image, 0, 0, $white);

        // Draw line representing the first range
        $rangeStart = HelperDate::getSumMinutesOfTimeString($range[0]);
        $rangeEnd   = HelperDate::getSumMinutesOfTimeString($range[1]);
        $y          = 1;

        \imageline($image, $rangeStart, $y, $rangeEnd, $y, $black);

        // Draw comparison ranges
        $y += $stepSize;

        foreach ($rangesCompare as $rangeCompare) {
            \imageline(
                $image,
                HelperDate::getSumMinutesOfTimeString($rangeCompare[0]),
                $y,
                HelperDate::getSumMinutesOfTimeString($rangeCompare[1]),
                $y,
                $black
            );

            $y += $stepSize;
        }

        // 1 = first range itself ;)
        $maxOverlaps = 1;

        // Scan pixels top-down from pixels of first range, find longest series of black pixels
        for ($x = $rangeStart; $x <= $rangeEnd; $x++) {
            $maxOverlapsCurrent = 0;

            for ($y = 1; $y <= $amountRanges; $y++) {
                $scannedColor = \imagecolorat($image, $x, $y);

                if ($scannedColor !== $white) {
                    $maxOverlapsCurrent++;
                }
            }

            $maxOverlaps = \max($maxOverlaps, $maxOverlapsCurrent);
        }

        \imagedestroy($image);

        return $maxOverlaps;
    }

    /**
     * Returns all occurring weekdays as numbers between two given dates
     *
     * @param  array|int|string $startTime
     * @param  array|int|string $endTime
     * @return array
     * @throws \Zend_Date_Exception
     */
    public static function getWeekDaysByTimeRange($startTime, $endTime): array
    {
        $weekDays = [];

        $day = new \Zend_Date($startTime);
        $end = (new \Zend_Date($endTime))->toValue();

        while ($day->toValue() <= $end) {
            $weekDays[] = $day->get(\Zend_Date::WEEKDAY_DIGIT);
            $day->add('1', \Zend_Date::DAY);
        }

        return $weekDays;
    }
}
