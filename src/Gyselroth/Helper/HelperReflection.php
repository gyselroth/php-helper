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

use Gyselroth\Helper\Exception\ReflectionException;
use Gyselroth\Helper\Exception\ReflectionExceptionInvalidType;
use Gyselroth\Helper\Exception\ReflectionExceptionUndefinedFunction;
use Gyselroth\Helper\Interfaces\ConstantsDataTypesInterface;

class HelperReflection implements ConstantsDataTypesInterface
{
    public const LOG_CATEGORY = 'reflectionhelper';

    /**
     * @param  array|bool|int|Object|string $value
     * @param  string                       $destinationType
     * @return array|bool|float|int|string
     * @throws ReflectionExceptionInvalidType
     */
    public static function getTypeCasted($value, string $destinationType)
    {
        switch ($destinationType) {
            case self::DATA_TYPE_ARRAY:
                return (array)$value;
            case self::DATA_TYPE_BOOL:
                return (bool)$value;
            case self::DATA_TYPE_FLOAT:
                return (float)$value;
            case self::DATA_TYPE_INT:
            case self::DATA_TYPE_INT_SHORT:
                return (int)$value;
            case self::DATA_TYPE_STRING:
                return (string)$value;
            default:
                LoggerWrapper::warning(
                    "Detected unhandled type: $destinationType",
                    [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY, LoggerWrapper::OPT_PARAMS => $destinationType]);
                /** @noinspection ThrowRawExceptionInspection */
                throw new ReflectionExceptionInvalidType();
        }
    }

    /**
     * @param  string $className
     * @param  string $config
     * @return Object              Dynamically created object of given class
     * @throws ReflectionException
     */
    public static function constructObject(string $className, $config = null)
    {
        try {
            self::ensureIsClass($className);
            $object = new $className($config);
        } catch (\Exception $exception) {
            $trace = \debug_backtrace();
            throw new ReflectionException($exception->getMessage() . ' Caller: ' . \print_r($trace[1], true));
        }

        return $object;
    }

    /**
     * Check whether given class exists and is defined (is non-empty)
     *
     * @param  string $className
     * @param  bool   $logIfNot Write entry into application log if given class name represents no constructable class
     * @param  string $logCategory
     * @return bool
     * @throws ReflectionException
     */
    public static function ensureIsClass(string $className, bool $logIfNot = false, string $logCategory = ''): bool
    {
        if (empty($className)) {
            $exception = 'Tried to construct undefined class.';
        } else {
            $exception = \class_exists($className)
                ? false
                : "Class not defined: '$className'.";
        }

        if ($exception) {
            if ($logIfNot) {
                LoggerWrapper::info(
                    $exception,
                    empty($logCategory) ? null : [LoggerWrapper::OPT_CATEGORY => $logCategory]);
            }

            throw new ReflectionException($exception);
        }

        return true;
    }

    public static function getControllerFilenames(string $modulePath): array
    {
        return HelperFile::scanDirRecursive($modulePath, 'Controller.php');
    }

    public static function getActionsFromControllerFile(string $pathController): array
    {
        \preg_match_all(
            '/(function) ([a-zA-Z]+)(Action)\(/',
            \file_get_contents($pathController),
            $matches);

        return $matches[2];
    }

    /**
     * Call user function. Works same as call_user_func(), but accepts also a string function reference like 'MyClass::myMethod'
     *
     * @param  string $funcRefString function reference
     * @return array|bool|int|string|Object|null
     * @throws ReflectionExceptionUndefinedFunction
     * @note   Additional arbitrary arguments required by called methods can be passed as additional arguments
     */
    public static function callUserFunction(string $funcRefString)
    {
        $funcArgs = \func_get_args();

        // Remove function reference
        \array_shift($funcArgs);

        if (!self::isFunctionReference($funcRefString)) {
            throw new ReflectionExceptionUndefinedFunction($funcRefString);
        }

        if (false !== \strpos($funcRefString, '::')) {
            $funcRefParts = \explode('::', $funcRefString);
            $callback = $funcRefParts;
        } else {
            LoggerWrapper::info('HelperReflection::callUserFunction() called function instead of method.');
            $callback = $funcRefString;
        }

        return \call_user_func_array($callback, $funcArgs);
    }

    /**
     * Call user function where parameters are stored in an array
     *
     * @param  string $funcRefString
     * @param  array  $funcArgs
     * @return bool|int|float|string|array|Object|null
     * @throws ReflectionException
     */
    public static function callUserFunctionArray(string $funcRefString, array $funcArgs)
    {
        if (!self::isFunctionReference($funcRefString)) {
            throw new ReflectionException('Function not found: ' . $funcRefString);
        }

        $funcRefParts = \explode('::', $funcRefString);

        return \call_user_func_array($funcRefParts, $funcArgs);
    }

    /**
     * Check whether given function/method reference is valid
     *
     * @param  string $funcRefString Format: function or class::method
     * @return bool
     */
    public static function isFunctionReference(string $funcRefString): bool
    {
        /** @noinspection ReturnFalseInspection */
        if (false === \strpos($funcRefString, '::')) {
            return \function_exists($funcRefString);
        }

        $parts = \explode('::', $funcRefString);

        return \method_exists($parts[0], $parts[1]);
    }

    /**
     * @param  string $pathPhpFilename
     * @param  string $classPrefix
     * @return bool|array|int|string|Object|resource
     */
    public static function getConstantFromPhpClassFile(string $pathPhpFilename, string $classPrefix = 'Helper')
    {
        $className = $classPrefix . \pathinfo($pathPhpFilename, PATHINFO_FILENAME);
        /** @noinspection ReturnFalseInspection */
        if (false !== \strpos($className, 'Mediator')) {
            return false;
        }

        $constantName = $className . '::LOG_CATEGORY';

        return \defined($constantName) ? eval('return ' . $constantName . ';') : false;
    }

    public static function getCallingMethodName(bool $withClassName = true): string
    {
        $previousBacktrace = \debug_backtrace()[1];

        return ($withClassName
                ? $previousBacktrace['class'] . '::'
                : ''
            ) . $previousBacktrace['function'];
    }


    public static function getCallee(): string
    {
        $callee = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3)[2];

        $class    = $callee['class'] ?? '';
        $function = $callee['function'] ?? '';

        return $class . '::' . $function . '()';
    }

    /**
     * @todo create new vendor-package "helper-zf1" (or "helper-in2"?) from methods ( or more):
     *
     * getControllerActionsByModule()
     * getDbModelByEntity()
     * getAllLogCategories()
     * getSortedSearchResultsByQuery()
     */
}
