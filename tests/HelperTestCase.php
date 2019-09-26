<?php

/**
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Tests;

use Gyselroth\Helper\LoggerWrapper;
use Gyselroth\Helper\HelperFile;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class HelperTestCase extends TestCase {

    protected $_pathToLogfile = __DIR__ . '/tmp/app.log';
    protected $_logMock;
    protected $_logger;

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        self::emptyTempFolder();
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpParamsInspection */
        $this->_logger = new LoggerWrapper($this->_setUpLogger(), true, '.');
    }

    protected function tearDown()
    {
        self::emptyTempFolder();
        if (\is_object($this->_logger)) {
            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection ImplicitMagicMethodCallInspection */
            $this->_logger->__destruct();
            $this->_logger = null;
        }
    }

    private function emptyTempFolder()
    {
        $tmpPath = HelperFile::getGlobalTmpPath();
        if (is_dir($tmpPath)) {
            HelperFile::rmdirRecursive($tmpPath);
        }
    }

    /**
     * @param  bool $useStdOut
     * @param  string $logLevel
     * @return Logger
     * @throws \Exception
     */
    private function _setUpLogger($useStdOut = false, $logLevel = 'DEBUG')
    {
        $path =  $useStdOut ? 'php://stdout' : __DIR__ . '/../var/logs/phpunit.log';

        return (new Logger('phpunit'))
//            ->pushProcessor(new Monolog\Processor\UidProcessor())
            ->pushHandler(new StreamHandler($path, $logLevel));
    }
}

