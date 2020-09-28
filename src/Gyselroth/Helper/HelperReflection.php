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

use Gyselroth\Helper\Exception\ReflectionException;
use Gyselroth\Helper\Exception\ReflectionExceptionInvalidType;
use Gyselroth\Helper\Exception\ReflectionExceptionUndefinedFunction;
use Gyselroth\Helper\Interfaces\ConstantsDataTypesInterface;

/**
 * @todo create new vendor-package "helper-zf1"
 *
 * getControllerActionsByModule()
 * getDbModelByEntity()
 * getAllLogCategories()
 * getSortedSearchResultsByQuery()
 * getRequestParamsOfControllerAction()
 * getSourceCodeOfControllerAction()
 * getPhpDocDescriptionOfControllerAction()
 */

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
                    [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY, LoggerWrapper::OPT_PARAMS => $destinationType]
                );

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
                    empty($logCategory) ? [] : [LoggerWrapper::OPT_CATEGORY => $logCategory]
                );
            }

            throw new ReflectionException($exception);
        }

        return true;
    }

    /**
     * @param string $modulePath
     * @return array
     * @deprecated
     */
    public static function getControllerFilenames(string $modulePath): array
    {
        return self::getZf1ControllerFilenames($modulePath);
    }

    public static function getZf1ControllerFilenames(string $modulePath): array
    {
        return HelperFile::scanDirRecursive($modulePath, 'Controller.php');
    }

    public static function getActionsFromControllerFile(string $pathController): array
    {
        $fileContent = \file_get_contents($pathController);

        if (!$fileContent) {
            return [];
        }

        \preg_match_all(
            '/(function) ([a-zA-Z]+)(Action)\(/',
            $fileContent,
            $matches
        );

        return $matches[2];
    }

    /**
     * Call user function. Works same as call_user_func(), but accepts also a string function reference
     * like 'MyClass::myMethod'
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

        return \defined($constantName)
            ? eval('return ' . $constantName . ';')
            : false;
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

    public static function getPhpDocDescriptionOfControllerAction(string $pathControllerFile, ?string $action): string
    {
        if (!\file_exists($pathControllerFile)) {
            return '';
        }

        $sourceCode = \file_get_contents($pathControllerFile);

        $offsetActionDeclaration = false === $sourceCode
            ? false
            : \stripos($sourceCode, ' function ' . $action . 'Action');

        if (false === $offsetActionDeclaration || false === $sourceCode) {
            return '';
        }

        $codeBeforeActionDeclaration = \substr($sourceCode, 0, $offsetActionDeclaration);

        $commentBlocks  = \explode('/**', $codeBeforeActionDeclaration);
        $phpDocOfAction = \array_pop($commentBlocks);

        if (null === $phpDocOfAction) {
            return '';
        }

        $firstLineOfPhpDoc = \explode("\n", $phpDocOfAction)[1];

        $description = \trim($firstLineOfPhpDoc, '* ');

        /** @noinspection SubStrUsedAsStrPosInspection */
        return '@' === $description[0]
        || '$' === $description[0]
            ? ''
            : $description;
    }

    /**
     * Get source code of controller action w/o leading Doc-comments
     *
     * @param  string $action
     * @param  string  $phpFileSource
     * @return string
     */
    public static function getSourceCodeOfControllerAction(?string $action, string $phpFileSource): string
    {
        $offsetActionDeclaration = \stripos($phpFileSource, ' function ' . $action . 'Action');

        if (false === $offsetActionDeclaration) {
            return '';
        }

        $functionPhp = \substr($phpFileSource, $offsetActionDeclaration);
        $functionPhp = \explode('public function', $functionPhp)[0];
        $functionPhp = \trim($functionPhp);

        while (!HelperString::endsWith($functionPhp, '}')) {
            $lines = \explode("\n", $functionPhp);
            \array_pop($lines);
            $functionPhp = \trim(\implode("\n", $lines));
        }

        return $functionPhp;
    }

    public static function getRequestParamsOfControllerAction(string $pathControllerFile, ?string $action): array
    {
        $sourceCode = \file_get_contents($pathControllerFile);

        $actionCode = $sourceCode
            ? self::getSourceCodeOfControllerAction($action, $sourceCode)
            : '';

        if ('' === $actionCode) {
            return [];
        }

        // 1. Extract request-params given like: $this->_request->getParam('<parameterName'>)
        \preg_match_all('/_request->getParam\(\'([a-z0-9_]+)\'\)/i', $actionCode, $matches);

        $requestParams = $matches[1];

        // 2. Extract request-params given like: $this->_request->getPost('<parameterName'>)
        \preg_match_all('/_request->getPost\(\'([a-z0-9_]+)\'\)/i', $actionCode, $matches);

        $requestParams = \array_merge($requestParams, $matches[1]);

        // 3. Find variable that array of request-params is filled-into, extract parameter names out of it
        \preg_match_all('/(\\$[a-z0-9_]+)\s*=\s*\\$this->_request->getParams\(\);/i', $actionCode, $matches);

        if (isset($matches[1][0])) {
            $requestParamsVariable = $matches[1][0];

            // 3.1. Find params taken out of request-params array variable
            \preg_match_all(
                '/\\' . $requestParamsVariable . '\[\'([a-z0-9_]+)\'\]/i',
                $actionCode,
                $matches
            );

            if ([] !== $matches[1]) {
                $requestParams = \array_merge($requestParams, $matches[1]);
            }
        }

        // 4. Find variable that array of request-post is filled-into, extract parameter names out of it
        \preg_match_all('/(\\$[a-z0-9_]+)\s*=\s*\\$this->_request->getPost\(\);/i', $actionCode, $matches);

        if (isset($matches[1][0])) {
            $requestParamsVariable = $matches[1][0];

            // 4.1. Find params taken out of request-post array variable
            \preg_match_all('/\\' . $requestParamsVariable . '\[\'([a-z0-9_]+)\'\]/i', $actionCode, $matches);

            if ([] !== $matches[1]) {
                $requestParams = \array_merge($requestParams, $matches[1]);
            }
        }

        $requestParams = \array_unique($requestParams);

        \sort($requestParams);

        return $requestParams;
    }
}
