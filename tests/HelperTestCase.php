<?php

/**
 * Copyright (c) 2017-2020 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Tests;

use Gyselroth\HelperLog\LoggerWrapper;
use Gyselroth\Helper\HelperFile;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class HelperTestCase extends TestCase
{
    protected $_pathToLogfile = __DIR__ . '/tmp/app.log';

    protected $_logMock;

    /** @var LoggerWrapper|null  */
    protected $_logger;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->emptyTempFolder();

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpParamsInspection */
        $this->_logger = new LoggerWrapper($this->_setUpLogger(), true, '.');
    }

    protected function tearDown(): void
    {
        $this->emptyTempFolder();

        if (\is_object($this->_logger)) {
            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection ImplicitMagicMethodCallInspection */
            $this->_logger->__destruct();
            $this->_logger = null;
        }
    }

    private function emptyTempFolder(): void
    {
        $tmpPath = HelperFile::getGlobalTmpPath();

        if (\is_dir($tmpPath)) {
            HelperFile::rmdirRecursive($tmpPath);
        }
    }

    /**
     * @param  bool $useStdOut
     * @param  string $logLevel
     * @return Logger
     * @throws \Exception
     */
    private function _setUpLogger($useStdOut = false, $logLevel = 'DEBUG'): Logger
    {
        $path =  $useStdOut ? 'php://stdout' : __DIR__ . '/../var/logs/phpunit.log';

        return (new Logger('phpunit'))
//            ->pushProcessor(new Monolog\Processor\UidProcessor())
            ->pushHandler(new StreamHandler($path, $logLevel));
    }
}
