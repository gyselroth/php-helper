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

use Gyselroth\Helper\Exception\ArrayException;
use Gyselroth\Helper\Interfaces\ConstantsDataTypesInterface;

class HelperArray implements ConstantsDataTypesInterface
{
    public const LOG_CATEGORY = 'arrayhelper';

    private const CASTABLE_TYPES = [
        self::DATA_TYPE_BOOL,
        self::DATA_TYPE_FLOAT,
        self::DATA_TYPE_INT_SHORT,
        self::DATA_TYPE_STRING
    ];

    /**
     * Constructor (non-public, not meant to be instantiated)
     *
     * @throws ArrayException
     */
    private function __construct()
    {
        throw new ArrayException('Invalid instantiation');
    }

    /**
     * Check whether given array is associative (has string keys, is not numerically indexed)
     *
     * @param  array $array
     * @param  bool  $allowStringEnumeratedKeys     Allow string-based keys, that correspond to a zero-based enumeration?
     * @return bool
     * @note   Only the 1st level of items is checked. For multi-level checking: iterate over all levels (or extend this method)
     */
    public static function isAssociative(array $array, bool $allowStringEnumeratedKeys = false): bool
    {
        if ([] === $array) {
            return false;
        }

        return $allowStringEnumeratedKeys
            ? self::hasStringKeys($array)
            : \array_keys($array) !== \range(0, \count($array) - 1);
    }

    /**
     * @param  array $array
     * @return bool
     */
    public static function isMultiDimensional(array $array): bool
    {
        foreach ($array as $item) {
            if (\is_array($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the given value is array accessible
     *
     * @param  array|Object $value
     * @return bool
     */
    public static function isAccessible($value): bool
    {
        return \is_array($value)
            || $value instanceof \ArrayAccess;
    }

    public static function hasStringKeys(array $array): bool
    {
        return \count(
            \array_filter(
                \array_keys($array),
                'is_string')
            ) > 0;
    }

    /**
     * @param  array|string|int $array  Type-casted to array if not an array
     * @param  bool  $makeItemsUnique
     * @param  bool  $convertNonNumericValuesToZero
     * @return array Given $array w/ all items converted to integers
     */
    public static function intVal(
        $array,
        bool $makeItemsUnique = false,
        bool $convertNonNumericValuesToZero = false
    ): array
    {
        if (!\is_array($array)) {
            if (\is_numeric($array)) {
                return [(int)$array];
            }
            return $convertNonNumericValuesToZero ? [0] : [];
        }

        $integers = [];
        foreach ($array as $value) {
            if ($convertNonNumericValuesToZero
                || \is_numeric($value)
            ) {
                $integers[] = (int)$value;
            }
        }

        return $makeItemsUnique ? \array_unique($integers) : $integers;
    }

    /**
     * Convenience-wrapper for HelperNumeric::intExplode
     *
     * @param  string|null $str
     * @param  string      $delimiter
     * @param  bool        $excludeNullValues
     * @return int[]
     */
    public static function intExplode(?string $str, string $delimiter = ',', bool $excludeNullValues = true): array
    {
        return HelperNumeric::intExplode($str, $delimiter, $excludeNullValues);
    }

    /**
     * @param  array|int|string  $array
     * @param  string $glue
     * @param  bool   $makeItemsUnique
     * @return string                   List of integers, all other data types out of $array are filtered out
     */
    public static function intImplode($array, string $glue = ',', bool $makeItemsUnique = true): string
    {
        return \is_array($array)
            ? \implode($glue, self::intVal($array, $makeItemsUnique))
            : (int)$array;
    }

    public static function trim(array $strings, bool $allowEmpty = false): array
    {
        $trimmed = [];
        foreach ($strings as $string) {
            if (!empty($string)
                || $allowEmpty
            ) {
                $trimmed[] = \trim($string);
            }
        }

        return $trimmed;
    }

    /**
     * Find item where contained value w/ given key is given value
     *
     * @param  array                                  $array
     * @param  string                                 $expectedKey
     * @param  array|float|int|resource|string|Object $expectedValue
     * @param  bool                                   $strict
     * @return bool|int|float|string|array|Object|null  Found item or null
     */
    public static function getItemByKeyValue(array $array, string $expectedKey, $expectedValue, bool $strict = false)
    {
        foreach ($array as $item) {
            if (array_key_exists($expectedKey, $item)) {
                if ($strict) {
                    if ($item[$expectedKey] === $expectedValue) {
                        return $item;
                    }
                } /** @noinspection TypeUnsafeComparisonInspection */
                elseif ($item[$expectedKey] == $expectedValue) {
                    return $item;
                }
            }
        }

        return null;
    }

    public static function containsSubstring(array $array, string $needleSubString, bool $caseSensitive = true): bool
    {
        foreach ($array as $item) {
            if ($caseSensitive) {
                /** @noinspection ReturnFalseInspection */
                if (false !== \strpos($item, $needleSubString)) {
                    // Found case-sensitively
                    return true;
                }
            } /** @noinspection ReturnFalseInspection */ elseif (false !== stripos($item, $needleSubString)) {
                // Found case-insensitively
                return true;
            }
        }

        return false;
    }

    /**
     * If there is a sub-array containing given key and value: Returns that sub-array's key, otherwise returns null.
     *
     * @param  int|string $expectedValue
     * @param  string     $expectedKey
     * @param  array      $array
     * @return int|string|null      the found item's key or null
     */
    public static function searchValueInMultidimensionalArray($expectedValue, string $expectedKey, array $array)
    {
        foreach ($array as $key => $item) {
            if (array_key_exists($expectedKey, $item)
                && $item[$expectedKey] === $expectedValue
            ) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @param  array                $array
     * @param  string               $keyOnLevel0
     * @param  string               $keyOnLevel1
     * @param  string               $keyOnLevel2
     * @param  string               $keyOnLevel3
     * @param  boolean|string|array $default
     * @return array|float|int|string|Object    Value on sub level(s), identified by given keys, or full array if no keys given. False if a given key doesn't exist
     */
    public static function getValueByKeyFromSubArrays(
        array $array,
        string $keyOnLevel0 = '',
        string $keyOnLevel1 = '',
        string $keyOnLevel2 = '',
        string $keyOnLevel3 = '',
        $default = false
    )
    {
        $hasKeyOnLevel3 = !empty($keyOnLevel3);
        $hasKeyOnLevel2 = !empty($keyOnLevel2);
        $hasKeyOnLevel1 = !empty($keyOnLevel1);
        $hasKeyOnLevel0 = !empty($keyOnLevel0);

        if ($hasKeyOnLevel3 && $hasKeyOnLevel2 && $hasKeyOnLevel1 && $hasKeyOnLevel0) {
            return $array[$keyOnLevel0][$keyOnLevel1][$keyOnLevel2][$keyOnLevel3] ?? $default;
        }
        if ($hasKeyOnLevel2 && $hasKeyOnLevel1 && $hasKeyOnLevel0) {
            return $array[$keyOnLevel0][$keyOnLevel1][$keyOnLevel2] ?? $default;
        }
        if ($hasKeyOnLevel1 && $hasKeyOnLevel0) {
            return $array[$keyOnLevel0][$keyOnLevel1] ?? $default;
        }
        if ($hasKeyOnLevel0) {
            return $array[$keyOnLevel0] ?? $default;
        }

        // No key on any level is given, return the whole configuration array
        return $array;
    }

    /**
     * @param  array  $array
     * @param  string $keyName  Default: 'id'
     * @param  bool   $sortById Default: false
     * @return array    Given array re-indexed with keys taken from the items' ID values
     */
    public static function keysFromIDs(array $array, string $keyName = 'id', bool $sortById = false): array
    {
        $arrayWithKeys = [];
        foreach ($array as $item) {
            $key                 = (int)$item[$keyName];
            $arrayWithKeys[$key] = $item;
        }
        if ($sortById) {
            ksort($arrayWithKeys);
        }
        return $arrayWithKeys;
    }

    public static function keysExist(array $arr, array $keys): bool
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $arr)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Perform given string-replacements recursively on all keys of given array
     *
     * @param  array|string $search
     * @param  array|string $replace
     * @param  array        &$array
     */
    public static function replaceInKeys($search, $replace, array &$array): void
    {
        $results = [];
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                self::replaceInKeys($search, $replace, $value);
            }
            $key           = \str_replace($search, $replace, $key);
            $results[$key] = $value;
        }

        $array = $results;
    }

    /**
     * Does the same like in_array just in an multidimensional array
     *
     * @param  int|string $needle   String to search for in array
     * @param  array      $haystack Array to be searched
     * @param  bool       $strict   Boolean if == or ===
     * @return bool
     */
    public static function inArrayRecursive($needle, array $haystack, bool $strict = false): bool
    {
        foreach ($haystack as $item) {
            /** @noinspection TypeUnsafeComparisonInspection */
            /** @noinspection NotOptimalIfConditionsInspection */
            if (($strict ? $item === $needle : $item == $needle)
                || (\is_array($item)
                    && self::inArrayRecursive($needle, $item, $strict)
                )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array|string $idsList      Comma separated relation-type prefixed IDs list, i.e.: pe_4016536,pe_4012942,cl_1516,lo_1,co_37794
     * @param  string       $filterPrefix Optional, if given: keep only items with this prefix
     * @param  string       $prefixGlue
     * @return array                        Array of integers from the given (prefixed) relation-IDs, w/o their relation-type prefix
     */
    public static function getArrayFromRelatedIdsList(
        $idsList,
        string $filterPrefix = '',
        string $prefixGlue = '_'
    ): array
    {
        $relatedIdsWithPrefix = \is_array($idsList) ? $idsList : \explode(',', $idsList);
        $relatedIds           = [];
        foreach ($relatedIdsWithPrefix as $relatedId) {
            $parts = \explode($prefixGlue, \trim($relatedId));

            if (empty($filterPrefix)
                || $parts[0] === $filterPrefix
            ) {
                $relatedIds[] = (int)$parts[1];
            }
        }

        return \array_unique($relatedIds);
    }

    public static function removeItemsByValue(array $array, array $values): array
    {
        /**
         * @note  The returned array will be associative, possibly with "gaps" in the otherwise numerically ordered keys (i.e. 1,2,4,...).
         *        When using the array in JavaScript, it's data type might be converted to object (to preserve the keys) instead of array.
         *        If usage of the result requires an un-associative array, use array_values() upon the array.
         */
        return array_diff($array, $values);
    }

    /**
     * @param  array  $array
     * @param  array  $values
     * @param  string $itemKey
     * @return array
     */
    public static function removeItemsByValues($array, $values, $itemKey): array
    {
        foreach ($array as $key => $item) {
            if (\in_array($item[$itemKey], $values, true)) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Converts data types of MySql query results
     *
     * PHP does not use data types of the MySql rows. Instead it returns all fields a string.
     * For use with Kendo UI we have to cast for instance integers.
     *
     * Possible field types:    int | array.int | array.string
     *
     * @param  array $queryResult
     * @param  array $dataTypes name/data type pair for each column to be converted
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public static function convertDataTypesOfQueryResult(array $queryResult, array $dataTypes): array
    {
        foreach ($queryResult as $index => $row) {
            foreach ($row as $field => $value) {
                if (!array_key_exists($field, $dataTypes)) {
                    continue;
                }

                switch ($dataTypes[$field]) {
                    case self::DATA_TYPE_ARRAY_OF_INTS:
                    case self::DATA_TYPE_ARRAY_OF_STRINGS:
                        $queryResult[$index][$field] = \strlen($value) > 0 ? \explode(',', $value) : [];
                        if (self::DATA_TYPE_ARRAY_OF_INTS === $dataTypes[$field]) {
                            foreach ($queryResult[$index][$field] as $fieldIndex => $id) {
                                $queryResult[$index][$field][$fieldIndex] = (int)$id;
                            }
                        }
                        break;
                    case self::DATA_TYPE_INT_SHORT:
                        $queryResult[$index][$field] = (int)$value;
                        break;
                    default:
                        LoggerWrapper::warning("Detected unhandled data type field: {$dataTypes[$field]}", [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY, LoggerWrapper::OPT_PARAMS => $dataTypes[$field]]);
                }
            }
        }

        return $queryResult;
    }

    /**
     * Converts all items' 'value' value into data type declared within items' 'type'
     *
     * @param  array $array
     * @return array
     */
    public static function convertArrayDataByTypes(array $array): array
    {
        $converted = [];
        foreach ($array as $key => $value) {
            switch (strtolower($value['type'])) {
                case self::DATA_TYPE_INT_SHORT:
                case self::DATA_TYPE_INT:
                    $converted[$key] = (int)$value['value'];
                    break;
                case self::DATA_TYPE_STRING:
                    $converted[$key] = (string)$value['value'];
                    break;
                case self::DATA_TYPE_ARRAY:
                    $converted[$key] = (array)$value['values']['value'];
                    break;
                case self::DATA_TYPE_OBJECT:
                    $converted[$key] = (object)$value['values']['value'];
                    break;
                default:
                    /**
                     * @todo If data type not set, the values should be handed over without any type changes. If key 'value' is set, single value is being passed. If key 'values' is set, an array is being passed
                     * @todo suggested solution (review and integrate)

                         if (array_key_exists('value', $value)) {
                             $converted[$key] = $value['value'];
                         } elseif (array_key_exists('values', $value)) {
                             $converted[$key] = $value['values']['value'] ?: null;
                         }
                         break;
                    **/
                    if ($value['value']) {
                        $converted[$key] = $value['values']['value'];
                    } else {
                        $converted[$key] = $value['values']['value'] ?: null;
                    }
                    break;
            }
        }

        return $converted;
    }

    /**
     * Add postfix until key is not yet used (will be unique) in given associative array
     *
     * @param  int|string $key
     * @param  array      $array
     * @param  string     $postFix
     * @return string
     */
    public static function getUniqueKey($key, array $array, string $postFix = '_1'): string
    {
        while (array_key_exists($key, $array)) {
            $key .= $postFix;
        }

        return $key;
    }

    /**
     * Collect unique values of given given key from sub-arrays in given array into new array
     *
     * @param  array  $array
     * @param  int|string $key
     * @return array
     */
    public static function arrayUniqueByKey(&$array, $key): array
    {
        $tmp    = [];
        $result = [];

        foreach ($array as $value) {
            if (array_key_exists($key, $value)
                && !\in_array($value[$key], $tmp, true)
            ) {
                $tmp[]    = $value[$key];
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * @param array      $array
     * @param int|string $key
     * @param int        $sortingMode
     */
    public static function arrayMultidimensionalSortByKey(array &$array, $key, int $sortingMode = SORT_ASC): void
    {
        $tmp = [];
        foreach ($array as $value) {
            $tmp[] = $value[$key];
        }

        array_multisort($tmp, $sortingMode, $array);
    }

    /**
     * Checks whether the string begins with the given value ($check)
     *
     * @param  array                                  $array
     * @param  int|string                             $key
     * @param  array|float|int|resource|string|Object $check
     * @param  int                                    $sort
     * @return array
     */
    public static function arrayMultidimensionalSortByKeyAndCheck(
        array $array,
        $key,
        $check,
        int $sort = SORT_NATURAL
    ): array
    {
        $specialCharacter = [];
        $stringBeginsWith = [];
        $string           = [];
        foreach ($array as $value) {
            if (ctype_punct($value[$key])) {
                // True if: string contains only printable characters, but no whitespace, no a-z|A-Z, no digits
                $specialCharacter[] = $value;
            } elseif ($value[$key]{0} == $check) {
                $stringBeginsWith[] = $value;
            } else {
                $string[] = $value;
            }
        }

        self::arrayMultidimensionalSortByKey($specialCharacter, $key, $sort);
        self::arrayMultidimensionalSortByKey($stringBeginsWith, $key, $sort);
        self::arrayMultidimensionalSortByKey($string, $key, $sort);

        return \array_merge($specialCharacter, $stringBeginsWith, $string);
    }

    /**
     * Get keys from all sub-levels of given array
     *
     * @param  array $arr
     * @return array
     */
    public static function keys_recursive(array $arr): array
    {
        $keys = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $key => $value) {
            $keys[] = $key;
        }

        return \array_unique($keys);
    }

    public static function addKeysToSubArray(array $array, array $keys): array
    {
        $return = [];
        foreach ($keys as $key) {
            foreach ($array[$key] as $index => $value) {
                $return[$index][$key] = $value;
            }
        }

        return $return;
    }

    /**
     * Manipulate all string items within given array using given SPL method
     *
     * @param  array  $arr
     * @param  string $functionName E.g. 'strtolower', 'strtoupper', 'ucfirst', etc.
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Exception
     */
    public static function strSplManipulate(array $arr, string $functionName): array
    {
        if (!\function_exists($functionName)) {
            LoggerWrapper::alert("Tried to call undefined function: $functionName", [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]);
            return $arr;
        }

        foreach ($arr as $index => $value) {
            if (\is_array($value)) {
                $arr[$index] = self::strSplManipulate($value, $functionName);
            } elseif (\is_string($value)) {
                $arr[$index] = $functionName($value);
            }
        }

        return $arr;
    }

    /**
     * Get last element out of array (w/o shortening like using array_pop with original array instance)
     *
     * @param  array $array
     * @return array|float|int|resource|string|Object
     */
    public static function getLastElement(array $array)
    {
        return array_pop($array);
    }

    /**
     * Reduce array to items w/ given keys, optionally: Exclude items with given filter values
     *
     * @param  array $keys
     * @param  array $array
     * @param  array $excludes e.g. ['id' => 500, 'label' => 'me not'] - will exclude all items having 'id' == 500 or 'label' == 'me not'
     * @param  bool  $strict
     * @return array
     */
    public static function getValuesByKeys(
        array $keys,
        array $array,
        array $excludes = [],
        bool $strict = false
    ): array
    {
        $values = [];
        foreach ($keys as $key) {
            $value = $array[$key];

            $include = true;
            foreach ($excludes as $excludeKey => $excludeValue) {
                if (!$strict) {
                    /** @noinspection TypeUnsafeComparisonInspection */
                    // @todo: Kay: $value is not an array
                    if ($value[$excludeKey] == $excludeValue) {
                        $include = false;
                    }
                } elseif ($value[$excludeKey] === $excludeValue) {
                    $include = false;
                }
            }

            if ($include) {
                $values[$key] = $value;
            }
        }

        return $values;
    }

    /**
     * Merge two (or more) arrays with same index numeric key
     * @todo:  The function is currently not working for more than two arrays
     *
     * @return array|false
     */
    public static function mergeArraysByArrayIndexID()
    {
        $arrays = \func_get_args();
        if (!$arrays[0]) {
            return false;
        }

        $data       = [];
        $arrayIndex = 0;
        $newData    = null;
        foreach ($arrays as $currentArray) {
            foreach ($currentArray as $currentArrayKey => $currentArrayValue) {
                if ($arrayIndex === 0) {
                    $data[$currentArrayKey][] = $currentArrayValue;
                } elseif (
                    $arrayIndex === 1
                    && (
                        isset($data[$currentArrayKey][0])
                        && !empty($data[$currentArrayKey][0])
                    )
                ) {
                    $newData[$currentArrayKey] = \array_merge($data[$currentArrayKey][0], $currentArrayValue);
                }
            }
            $arrayIndex++;
        }

        return $newData ?: false;
    }

    /**
     * Exchange given old key into given new key, recursively for all items and contained sub-arrays
     *
     * @param  array      $array
     * @param  int|string $newKey
     * @param  int|string $oldKey
     * @return array
     */
    public static function changeKeyName(array $array, $newKey, $oldKey): array
    {
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $array[$key] = self::changeKeyName($value, $newKey, $oldKey);
            } else {
                $array[$newKey] = $array[$oldKey];
            }
        }

        unset($array[$oldKey]);

        return $array;
    }

    /**
     * @param  array  $array
     * @param  string $glue
     * @param  array  $wrap
     * @return string
     */
    public static function getCsvFromArray(array $array, $glue = ',', array $wrap = []): string
    {
        $csv = [];
        foreach ($array as $item) {
            $csv[] = \is_array($item) ? self::getCsvFromArray($item) : $item;
        }

        if (!empty($wrap)) {
            foreach ($csv as $index => $value) {
                $csv[$index] = $wrap[0] . $value . $wrap[1];
            }
        }

        return \implode($glue, $csv);
    }

    public static function getArrayFromCsvInRows(string $csv, string $delimiter = ','): array
    {
        $res      = [];
        $csvArray = str_getcsv(str_replace(["\n", "\r\r"], "\r", $csv), "\r");
        foreach ($csvArray as $line) {
            $res[] = array_map('trim', str_getcsv(trim($line), $delimiter));
        }

        return $res;
    }

    /**
     * Perform substr() on all keys of given array
     *
     * @param  array    $array
     * @param  int      $start
     * @param  int|null $length
     * @return array
     */
    public static function substrKeys(array $array, int $start, $length = null): array
    {
        $keys   = \array_keys($array);
        $return = [];
        foreach ($keys as $key) {
            // If length is given and is 0, false or null; an empty string will be returned.
            $keyName          = $length ? substr($key, $start, $length) : substr($key, $start);
            $return[$keyName] = $array[$key];
        }

        return $return;
    }

    public static function strReplaceAssociative(string $string, array $replacements): string
    {
        foreach ($replacements as $search => $replace) {
            $string = \str_replace($search, $replace, $string);
        }

        return $string;
    }

    public static function implodeWrapped(
        array $items,
        string $wrapLhs = "'",
        string $wrapRhs = "'",
        string $glue = ','
    ): string
    {
        $itemsWrapped = [];
        foreach ($items as $item) {
            $itemsWrapped[] = $wrapLhs . $item . $wrapRhs;
        }

        return \implode($glue, $itemsWrapped);
    }

    /**
     * Sort entries by value of item w/ given key
     *
     * @param  array  $a
     * @param  array  $b
     * @param  string $key
     * @param  bool   $strict
     * @return int
     */
    public static function sortByKey(
        array $a,
        array $b,
        string $key = 'time',
        bool $strict = false
    ): int
    {
        if (!$strict) {
            /** @noinspection TypeUnsafeComparisonInspection */
            if ($a[$key] == $b[$key]) {
                return 0;
            }
        } elseif ($a[$key] === $b[$key]) {
            return 0;
        }

        return $a[$key] > $b[$key] ? -1 : 1;
    }

    public static function arrayStrPos(array $needles, string $haystack): bool
    {
        foreach ($needles as $needle) {
            /** @noinspection ReturnFalseInspection */
            if (false !== \strpos($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array      $rows
     * @param  int|string $associativeIndexKey
     * @return array
     */
    public static function reIndexByKey(array $rows, $associativeIndexKey): array
    {
        $associativeResult = [];
        foreach ($rows as $row) {
            $associativeResult[$row[$associativeIndexKey]] = $row;
        }

        return $associativeResult;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array                                  $array
     * @param  int|string                             $key
     * @param  array|float|int|resource|string|Object $value
     * @return array
     */
    public static function set(array &$array, $key, $value): array
    {
        if (null === $key) {
            /** @noinspection UselessReturnInspection */
            return $array = $value;
        }

        $keys = \explode('.', $key);
        while (\count($keys) > 1) {
            $key = \array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array to hold the next value,
            // allowing us to create the arrays to hold final values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key])
                || !\is_array($array[$key])
            ) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[\array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Get an item from an array using "dot" notation
     *
     * @param  \ArrayAccess|array                     $array
     * @param  int|string                             $key
     * @param  array|float|int|resource|string|Object $default
     * @return array|float|int|resource|string|Object
     */
    public static function get($array, $key, $default = null)
    {
        if (!static::isAccessible($array)) {
            return $default instanceof \Closure ? $default() : $default;
        }

        if (null === $key) {
            return $array;
        }
        if (static::keyExists($array, $key)) {
            return $array[$key];
        }

        foreach (\explode('.', $key) as $segment) {
            if (!static::isAccessible($array)
                || !static::keyExists($array, $segment)
            ) {
                return $default instanceof \Closure ? $default() : $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Check whether the given key exists in the provided array or ArrayAccess-object
     *
     * @param  \ArrayAccess|array $array
     * @param  int|string         $key
     * @return bool
     */
    public static function keyExists($array, $key): bool
    {
        return $array instanceof \ArrayAccess
            ? $array->offsetExists($key)
            : \array_key_exists($key, $array);
    }

    /**
     * Merges non existing items like array_merge, but if a key exists, then the values will be merged as array.
     *
     * @param array $array
     * @param array $extension
     * @return array
     */
    public static function extendArray(array $array, array $extension = []): array
    {
        foreach ($extension as $key => $value) {
            $array[$key] =
                \array_key_exists($key, $array)
                && $value !== $array[$key]
                    ? \array_merge((array)$array[$key], (array)$value)
                    : $value;
        }

        return $array;
    }

    /**
     * @param  array $data
     * @return array|bool
     */
    public static function getAssociativeKeyValues(array $data)
    {
        if (empty($data)) {
            return false;
        }

        $array = [];
        foreach ($data as $key => $value) {
            $array[] = [
                'key'   => $key,
                'value' => $value
            ];
        }

        return $array;
    }

    /**
     * @param  array $elementsUnsorted
     * @return array|bool
     */
    public static function sortElements($elementsUnsorted)
    {
        $sortedElements = [];
        $result         = [];
        foreach ($elementsUnsorted as $unsortedKey => $elements) {
            if (isset($elementsUnsorted[$unsortedKey]['sort'])) {
                $sortedElements[$elements['sort']][$unsortedKey] = $elementsUnsorted[$unsortedKey];
            }
        }
        \ksort($sortedElements);
        foreach ($sortedElements as $elements) {
            foreach ($elements as $elementKey => $elementItems) {
                $result[$elementKey] = $elementItems;
            }
        }

        return [] === $result ? false : $result;
    }

    /**
     * Get all values of given array that are strings
     *
     * @param  array $arr
     * @return array
     */
    public static function extractStringValues(array $arr): array
    {
        $strings = [];
        foreach (\array_values($arr) as $value) {
            if (\is_string($value)) {
                $strings[] = $value;
            }
        }

        return $strings;
    }

    /**
     * Reform items of given key(s) of all sub-arrays (1 level of depth) of given array to given type
     *
     * @param  array        $array
     * @param  array|string $column Multiple keys as array | One key as string
     * @param  string       $type
     * @return array
     */
    public static function castSubColumn(array $array, $column, string $type = 'int'): array
    {
        if (!\in_array($type, self::CASTABLE_TYPES, true)) {
            return $array;
        }
        if (!\is_array($column)
            && !\is_iterable($column)
        ) {
            $column = (array)$column;
        }

        foreach ($array as $index => $subArray) {
            foreach ($column as $key) {
                \settype($subArray[$key], $type);
            }
            $array[$index] = $subArray;
        }

        return $array;
    }

    /**
     * Reform items of given key(s) of all sub-arrays (1 level of depth) of given array to integer
     *
     * @param  array        $array
     * @param  array|string $keys Multiple keys as array | One key as string
     * @return array
     * @deprecated use new method castValSubItemsByKey() instead
     */
    public static function intValSubItemsByKey(array $array, $keys): array
    {
        return self::castSubColumn($array, $keys);
    }

    public static function isIterable($var): bool
    {
        return $var !== null
            && (
                \is_array($var)
                || $var instanceof Traversable
                || $var instanceof Iterator
                || $var instanceof IteratorAggregate
            );
    }

    /**
     * Get array from (e.g. stdClass) object
     *
     * @param  object|array $obj
     * @return array
     */
    public static function objectToArray($obj): array
    {
        if (\is_object($obj)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $obj = \get_object_vars($obj);
        }

        return \is_array($obj)
            // Return array converted to object Using __FUNCTION__ (Magic constant) for recursive call
            ? $obj
            : (array)$obj;
    }

    public static function resortByDate(array &$array, string $dateColumnKey): void
    {
        \usort(
            $array,
            function ($a, $b) use ($dateColumnKey) {
                $dateA = \DateTime::createFromFormat('d.m.Y', $a[$dateColumnKey])->format('Ymd');
                $dateB = \DateTime::createFromFormat('d.m.Y', $b[$dateColumnKey])->format('Ymd');

                return $dateA - $dateB;
            }
        );
    }

    public static function sanitize(
        array &$array,
        bool $allowCharacters = true,
        bool $allowUmlauts = false,
        bool $allowDigits = false,
        bool $allowWhiteSpace = false,
        bool $allowSpace = false,
        string $allowedSpecialCharacters = ''
    ): void
    {
        foreach ($array as &$value) {
            if (\is_array($value)) {
                self::sanitize(
                    $value,
                    $allowCharacters,
                    $allowUmlauts,
                    $allowDigits,
                    $allowWhiteSpace,
                    $allowSpace,
                    $allowedSpecialCharacters);
            } elseif (!HelperString::validateString(
                $value,
                $allowCharacters,
                $allowUmlauts,
                $allowDigits,
                $allowWhiteSpace,
                $allowSpace,
                $allowedSpecialCharacters
            )) {
                $value = '';
            }
        }
    }

    /**
     * Extract given associative array into a "flat" array, containing just the values of the given key, of all items
     *
     * @param  array  $arr
     * @param  string $key
     * @return array
     * @throws \Exception
     * @deprecated
     */
    public static function flatten(array $arr, string $key): array
    {
        LoggerWrapper::warning(
            'Used deprecated HelperArray::flatten() - better: use array_column()',
            [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]);

        return \array_column($arr, $key);
    }

    /**
     * @deprecated
     * @param  \ArrayAccess|array $array
     * @param  int|string         $key
     * @return bool
     */
    public static function exists($array, $key): bool
    {
        return self::keyExists($array, $key);
    }

    /**
     * @deprecated
     * @param array $elements
     * @return array|bool
     */
    public static function sortElementArr(array $elements)
    {
        return self::sortElements($elements);
    }
}
