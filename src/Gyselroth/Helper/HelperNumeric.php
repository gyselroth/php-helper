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

use Gyselroth\Helper\Interfaces\ConstantsCountryCodes;
use Gyselroth\Helper\Interfaces\ConstantsUnitsOfDataMeasurementInterface;
use Gyselroth\HelperLog\LoggerWrapper;

class HelperNumeric implements ConstantsUnitsOfDataMeasurementInterface, ConstantsCountryCodes
{
    /**
     * @param  int|string $number
     * @param  int        $digits
     * @return string
     */
    public static function formatAmountDigits($number, int $digits): string
    {
        $number = (int)$number;

        while (\strlen((string)$number) < $digits) {
            $number = '0' . $number;
        }

        return (string)$number;
    }

    /**
     * @param  array  $array
     * @param  string $glue Default: ','
     * @param  bool   $sort
     * @param  bool   $makeUnique
     * @param  bool   $onlyPositive
     * @return string Imploded (list of) integers
     */
    public static function intImplode(
        array $array,
        string $glue = ',',
        bool $sort = true,
        bool $makeUnique = false,
        bool $onlyPositive = false
    ): string {
        $array = \array_unique($array);

        $integers = [];

        foreach ($array as $item) {
            if (\is_numeric($item)
                && (!$onlyPositive || $item > 0)
            ) {
                $integers[] = (int)$item;
            }
        }

        if ($sort) {
            \asort($integers);
        }

        if ($makeUnique) {
            $integers = \array_unique($integers);
        }

        return \implode($glue, $integers);
    }

    /**
     * Split given list of values by given delimiter into a unique array of integers
     *
     * @param  string|null $str
     * @param  string      $delimiter
     * @param  bool        $excludeNullValues Include null values (converted to 0)? Default: true
     * @param  bool        $unique
     * @return int[]
     */
    public static function intExplode(
        ?string $str,
        string $delimiter = ',',
        bool $excludeNullValues = true,
        bool $unique = false
    ): array {
        if (null === $str) {
            return [];
        }

        $parts = \explode($delimiter, $str);

        if (false === $parts) {
            return [];
        }

        $numbers = [];

        foreach ($parts as $number) {
            if (!$excludeNullValues || 'null' !== \strtolower($number)) {
                $numbers[] = (int)$number;
            }
        }

        return $unique
            ? \array_unique($numbers)
            : $numbers;
    }

    /**
     * Split given list of values by given delimiter into a unique array of float values
     *
     * @param string $str
     * @param string $delimiter
     * @param bool   $excludeNullValues Include null values (converted to 0)? Default: true
     * @param bool   $unique
     * @return array
     */
    public static function floatExplode(
        string $str,
        string $delimiter = ',',
        bool $excludeNullValues = true,
        bool $unique = false
    ): array {
        if ('' === $str) {
            return [];
        }

        $parts = \explode($delimiter, $str);

        if (false === $parts) {
            // @todo add logging
            return [];
        }

        $numbers = [];

        foreach ($parts as $number) {
            if (!$excludeNullValues || 'null' !== \strtolower($number)) {
                $numbers[] = (float)$number;
            }
        }

        return $unique
            ? \array_unique($numbers)
            : $numbers;
    }

    /**
     * Get size and unit (bytes, kilo or megabytes) values from given amount
     *
     * @param  int $bytes Size
     * @return array      Array w/ 'size' and 'unit'
     */
    public static function calcBytesSize($bytes): array
    {
        $bytes = (int)$bytes;

        if ($bytes < 1000) {
            return [
                'size' => $bytes,
                'unit' => self::UNIT_BYTES
            ];
        }

        $kilo = $bytes / 1024;

        if ($kilo < 1000) {
            return [
                'size' => \round($kilo, 1),
                'unit' => self::UNIT_KILOBYTES
            ];
        }

        $mega = $bytes / 1024000;

        return [
            'size' => \round($mega, 1),
            'unit' => self::UNIT_MEGABYTES
        ];
    }

    /**
     * @param  float|int $amountFull
     * @param  float|int $amountPartial
     * @return float|int
     */
    public static function getPercentage($amountFull, $amountPartial)
    {
        return empty($amountFull) || $amountFull === $amountPartial
            ? 100
            : $amountPartial / $amountFull * 100;
    }

    public static function removeEmptyItemsFromIDsCsv(string $ids): string
    {
        $entityIds = \array_filter(\explode(',', $ids));

        return \implode(',', $entityIds);
    }

    /**
     * @param float $amount
     * @param string $iso3166CountryCode
     * @param int $amountDigitsAfterDecimalSeparator
     * @return string
     * @throws \Exception
     */
    public static function formatMoney(
        float $amount,
        string $iso3166CountryCode = self::COUNTRY_CODE_SWITZERLAND,
        int $amountDigitsAfterDecimalSeparator = 3
    ): string {
        $iso3166CountryCode = \strtolower($iso3166CountryCode);

        switch ($iso3166CountryCode) {
            case self::COUNTRY_CODE_GERMANY:
                return \number_format($amount, $amountDigitsAfterDecimalSeparator, ',', '.');
            case self::COUNTRY_CODE_SWITZERLAND:
                return \number_format($amount, $amountDigitsAfterDecimalSeparator, '.', "'");
            case self::COUNTRY_CODE_UNITED_STATES:
                return \number_format($amount, $amountDigitsAfterDecimalSeparator, '.', ',');
            default:
                LoggerWrapper::warning('formatMoney() called w/ unhandled country code: ' . $iso3166CountryCode);

                return (string)$amount;
        }
    }
}
