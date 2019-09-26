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

use Gyselroth\Helper\Exception\LoggerException;
use Monolog\Handler\StreamHandler;

/**
 * Logger Wrapper - Optional service container for using logger component of different application host frameworks
 * Simple service container for DI into static and framework-agnostic helper classes
 *
 * @package Gyselroth\Helper
 */
class LoggerWrapper
{
    // Options items keys
    public const OPT_CATEGORY = 'category';
    public const OPT_PARAMS   = 'parameters';

    /**
     * Alert:     Action must be taken immediately
     * Emergency: System is unusable
     * Notice:    Normal but significant condition
     */
    public const PRIORITY_PSR3_EMERGENCY = 'emergency';
    public const PRIORITY_PSR3_ALERT     = 'alert';
    public const PRIORITY_PSR3_CRITICAL  = 'critical';
    public const PRIORITY_PSR3_ERROR     = 'error';
    public const PRIORITY_PSR3_WARNING   = 'warning';
    public const PRIORITY_PSR3_NOTICE    = 'notice';
    public const PRIORITY_PSR3_INFO  = 'info';
    public const PRIORITY_PSR3_DEBUG = 'debug';

    private const ZF1_PRIORITIES = [
        self::PRIORITY_PSR3_DEBUG     => 7,
        self::PRIORITY_PSR3_INFO      => 6,
        self::PRIORITY_PSR3_NOTICE    => 5,
        self::PRIORITY_PSR3_WARNING   => 4,
        self::PRIORITY_PSR3_ERROR     => 3,
        self::PRIORITY_PSR3_CRITICAL  => 2,
        self::PRIORITY_PSR3_ALERT     => 1,
        self::PRIORITY_PSR3_EMERGENCY => 0
    ];

    private const MONOLOG_LEVELS = [
        self::PRIORITY_PSR3_DEBUG     => 100,
        self::PRIORITY_PSR3_INFO      => 200,
        self::PRIORITY_PSR3_NOTICE    => 250,
        self::PRIORITY_PSR3_WARNING   => 300,
        self::PRIORITY_PSR3_ERROR     => 400,
        self::PRIORITY_PSR3_CRITICAL  => 500,
        self::PRIORITY_PSR3_ALERT     => 550,
        self::PRIORITY_PSR3_EMERGENCY => 600
    ];

    /** @var LoggerWrapper */
    protected static $instance;

    /** @var string */
    protected static $loggerClassName;

    /** @var \Monolog\Logger */
    protected static $logger;

    /** @var bool */
    protected static $isDevEnvironment;

    /** @var string */
    protected static $logPath;

    /**
     * Constructor
     *
     * @param  callable|string $loggerReference PSR-7 logger class callback, or logger class name as string
     * @param  bool            $isDevEnvironment
     * @param  string          $logPath
     * @throws LoggerException
     * @singleton
     */
    public function __construct($loggerReference, bool $isDevEnvironment = false, string $logPath = '')
    {
        if (null === self::$instance) {
            if (!\is_object($loggerReference)) {
                throw new LoggerException('first argument to LoggerWrapper::__construct must be an object (logger)');
            }
            self::$logger           = $loggerReference;
            self::$logPath          = $logPath;
            self::$isDevEnvironment = $isDevEnvironment;
            self::$instance         = $this;
        }
    }

    /**
     * Mute output
     *
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public static function mute(): void
    {
        new LoggerWrapper(function(){});
    }

    /**
     * Destructor: unset instance
     */
    public function __destruct()
    {
        self::$instance = null;
    }

    /**
     * @return bool
     */
    public static function isDevEnvironment(): bool
    {
        return (bool)self::$isDevEnvironment;
    }

    /**
     * Undefined method handler.
     *
     * @param  string $method
     * @param  array  $params
     * @throws LoggerException
     */
    public function __call(string $method, array $params): void
    {
        self::call($method, $params);
    }

    /**
     * Undefined method handler. Implements shortcut methods to log() w/ status - crit(), debug(), error(), info(), warning()
     *
     * @param  string $method
     * @param  array  $params
     * @throws LoggerException
     */
    private static function call(string $method, array $params): void
    {
        $priority = strtoupper($method);

        if ([] === $params) {
            throw new LoggerException('Missing log message');
        }

        /** @noinspection ReturnNullInspection */
        $message = \array_shift($params);
        /** @noinspection ReturnNullInspection */
        $extras = [] !== $params ? \array_shift($params) : null;

        self::log($message, $priority, $extras);
    }

    public static function log(string $message, string $priority = self::PRIORITY_PSR3_INFO, array $options = []): void
    {
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        if (self::$logger instanceof \Monolog\Logger) {
            // TODO: should logger wrapper really have to care about logfile and loglevel?
            if (empty(self::$logger->getHandlers())) {
                $streamHandler = new StreamHandler(self::$logPath, self::PRIORITY_PSR3_INFO);
                self::$logger->pushHandler($streamHandler);
            }
            self::$logger->log(self::getMonologLevelByPsr3($priority), $message, $options);
        } else {
            self::$logger->log($message, self::getZf1PriorityByPsr3($priority), $options);
        }
    }

    /**
     * @param  string $message
     * @param  array  $options
     * @throws \Exception
     */
    public static function alert(string $message, array $options = []): void
    {
        if (null !== self::$instance) {
            self::log($message, self::PRIORITY_PSR3_ALERT, $options);
        }
    }

    /**
     * @param  string $message
     * @param  array  $options
     * @throws \Exception
     */
    public static function crit(string $message, array $options = []): void
    {
        if (null !== self::$instance) {
            self::log($message, self::PRIORITY_PSR3_CRITICAL, $options);
        }
    }

    /**
     * @param  string $message
     * @param  array  $options
     * @throws \Exception
     */
    public static function debug(string $message, array $options = []): void
    {
        if (null !== self::$instance) {
            self::log($message, self::PRIORITY_PSR3_DEBUG, $options);
        }
    }

    /**
     * @param  string $message
     * @param  array  $options
     * @throws \Exception
     */
    public static function emerg(string $message, array $options = []): void
    {
        if (null !== self::$instance) {
            self::log($message, self::PRIORITY_PSR3_EMERGENCY, $options);
        }
    }

    /**
     * @param  string $message
     * @param  array  $options
     * @throws \Exception
     */
    public static function error(string $message, array $options = []): void
    {
        if (null !== self::$instance) {
            self::log($message, self::PRIORITY_PSR3_ERROR, $options);
        }
    }

    /**
     * @param  string $message
     * @param  array  $options
     * @throws \Exception
     */
    public static function info(string $message, array $options = []): void
    {
        if (null !== self::$instance) {
            self::log($message, self::PRIORITY_PSR3_INFO, $options);
        }
    }

    /**
     * @param  string $message
     * @param  array  $options
     * @throws \Exception
     */
    public static function notice(string $message, array $options = []): void
    {
        if (null !== self::$instance) {
            self::log($message, self::PRIORITY_PSR3_NOTICE, $options);
        }
    }

    /**
     * @param  string $message
     * @param  array  $options
     * @throws \Exception
     */
    public static function warning(string $message, array $options = []): void
    {
        if (null !== self::$instance) {
            self::log($message, self::PRIORITY_PSR3_WARNING, $options);
        }
    }

    /**
     * @param  string                                $message
     * @param  bool                                  $logAsError
     * @param  array|bool|int|Object|resource|string $returnValue
     * @param  string                                $logCategory
     * @return array|bool|int|Object|resource|string
     * @throws \Exception
     */
    public static function logOrDieOnDev(
        string $message,
        bool $logAsError = false,
        $returnValue = null,
        string $logCategory = null
    )
    {
        if (self::$isDevEnvironment) {
            die($message . "\n");
        }
        if (false !== $logAsError) {
            self::error($message, [self::OPT_CATEGORY => $logCategory]);
        }

        return $returnValue;
    }

    private static function getZf1PriorityByPsr3(string $priority = ''): int
    {
        return \array_key_exists($priority, self::ZF1_PRIORITIES)
            ? self::ZF1_PRIORITIES[$priority]
            : self::ZF1_PRIORITIES[self::PRIORITY_PSR3_INFO];
    }

    private static function getMonologLevelByPsr3(string $priority = ''): int
    {
        $priority = \strtolower($priority);

        return \array_key_exists($priority, self::MONOLOG_LEVELS)
            ? self::MONOLOG_LEVELS[$priority]
            : self::MONOLOG_LEVELS[self::PRIORITY_PSR3_INFO];
    }
}
