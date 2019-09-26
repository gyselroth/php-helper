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
use Tests\Fixtures\CustomLogMock;
use Tests\Fixtures\MonologMock;
use Tests\Fixtures\TestFilesHelper;


class LoggerWrapperTest extends HelperTestCase
{
    protected $_pathToLogfile = __DIR__ . '/tmp/app.log';
    protected $_logMock;
    protected $_logger;

    protected function setUp()
    {
        TestFilesHelper::emptyTmpDirectory();
    }

    protected function tearDown()
    {
        TestFilesHelper::removeTmpDirectory();
        if (\is_object($this->_logger)) {
            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection ImplicitMagicMethodCallInspection */
            $this->_logger->__destruct();
            $this->_logger = null;
        }
    }

//    /**
//     * Test LoggerWrapper::__construct without $loggerReference being an object
//     */
//    public function testLoggerWrapperConstructWithoutLoggerObject()
//    {
//        $this->_logger = new LoggerWrapper('test');
//    }

    /**
     * Test LoggerWrapper::isDevEnvironment without $isDevEnvironment
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testIsDevEnvironmentDefault()
    {
        $this->_logger = new LoggerWrapper($this->createMock(CustomLogMock::class));
        $this->assertFalse($this->_logger->isDevEnvironment());
    }

    /**
     * Test LoggerWrapper::isDevEnvironment with $isDevEnvironment = true
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testIsDevEnvironmentTrue()
    {
        $this->_logger = new LoggerWrapper($this->createMock(CustomLogMock::class), true);
        $this->assertTrue($this->_logger->isDevEnvironment());
    }

//    /**
//     * Test LoggerWrapper::isDevEnvironment with $isDevEnvironment = false
//     * @throws \Gyselroth\Helper\Exception\LoggerException
//     */
//    public function testIsDevEnvironmentFalse()
//    {
//        $this->_logger = new LoggerWrapper($this->createMock(CustomLogMock::class), false);
//        $this->assertFalse($this->_logger::isDevEnvironment());
//    }

    /**
     * Test LoggerWrapper::log default priority (info) to custom log (e.g. Zend_Log)
     * @throws \Exception
     */
    public function testLogInfoToCustomLog()
    {
        $this->_logger = $this->_setUpCustomLogger();
        $this->_logger->log('test message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'test message [priority: 6]');
    }

    /**
     * Test LoggerWrapper::log emergency message with log options to custom log
     * @throws \Exception
     */
    public function testLogEmergencyWithOptionsToCustomLog()
    {
        $this->_logger = $this->_setUpCustomLogger();
        $this->_logger->log('test message', 'emergency', ['option_1', 'option_2', 'option_3']);
        $this->assertStringEqualsFile($this->_pathToLogfile, 'test message [priority: 0] [options: option_1, option_2, option_3]');
    }

    /**
     * Test LoggerWrapper::log info to monolog with Streamhandler - no options
     * @throws \Exception
     */
    public function testLogInfoToMonolog()
    {
        $this->_logger = $this->_setUpMonoLogger();
        $this->_logger->log('test message', 'info');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'test message [priority: 200]');
    }

    /**
     * Test LoggerWrapper::log critical message to monolog without Streamhandler - no options
     * @throws \Exception
     */
    public function testLogCriticalToMonologWithoutStreamHandler()
    {
        $this->_logger = $this->_setUpMonoLogger(false);
        $this->_logger->log('test message', 'critical');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'test message [priority: 500]');
    }

    /**
     * Test LoggerWrapper::alert to custom log
     * @throws \Exception
     */
    public function testAlertToCustomLog()
    {
        $this->_logger = $this->_setUpCustomLogger();
        $this->_logger->alert('alert message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'alert message [priority: 1]');
    }

    /**
     * Test LoggerWrapper::alert to mono log
     * @throws \Exception
     */
    public function testAlertToMonoLog()
    {
        $this->_logger = $this->_setUpMonoLogger();
        $this->_logger->alert('alert message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'alert message [priority: 550]');
    }

    /**
     * Test LoggerWrapper::crit to custom log
     * @throws \Exception
     */
    public function testCritToCustomLog()
    {
        $this->_logger = $this->_setUpCustomLogger();
        $this->_logger->crit('crit message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'crit message [priority: 2]');
    }

    /**
     * Test LoggerWrapper::crit to mono log
     * @throws \Exception
     */
    public function testCritToMonoLog()
    {
        $this->_logger = $this->_setUpMonoLogger();
        $this->_logger->crit('crit message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'crit message [priority: 500]');
    }

    /**
     * Test LoggerWrapper::debug to custom log
     * @throws \Exception
     */
    public function testDebugToCustomLog()
    {
        $this->_logger = $this->_setUpCustomLogger();
        $this->_logger->debug('debug message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'debug message [priority: 7]');
    }

    /**
     * Test LoggerWrapper::debug to mono log
     * @throws \Exception
     */
    public function testDebugToMonoLog()
    {
        $this->_logger = $this->_setUpMonoLogger();
        $this->_logger->debug('debug message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'debug message [priority: 100]');
    }

    /**
     * Test LoggerWrapper::emerg to custom log
     * @throws \Exception
     */
    public function testEmergToCustomLog()
    {
        $this->_logger = $this->_setUpCustomLogger();
        $this->_logger->emerg('emerg message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'emerg message [priority: 0]');
    }

    /**
     * Test LoggerWrapper::emerg to mono log
     * @throws \Exception
     */
    public function testEmergToMonoLog()
    {
        $this->_logger = $this->_setUpMonoLogger();
        $this->_logger->emerg('emerg message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'emerg message [priority: 600]');
    }

    /**
     * Test LoggerWrapper::error to custom log
     * @throws \Exception
     */
    public function testErrorToCustomLog()
    {
        $this->_logger = $this->_setUpCustomLogger();
        $this->_logger->error('error message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'error message [priority: 3]');
    }

    /**
     * Test LoggerWrapper::error to mono log
     * @throws \Exception
     */
    public function testErrorToMonoLog()
    {
        $this->_logger = $this->_setUpMonoLogger();
        $this->_logger->error('error message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'error message [priority: 400]');
    }

    /**
     * Test LoggerWrapper::info to custom log
     * @throws \Exception
     */
    public function testInfoToCustomLog()
    {
        $this->_logger = $this->_setUpCustomLogger();
        $this->_logger->info('info message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'info message [priority: 6]');
    }

    /**
     * Test LoggerWrapper::info to mono log
     * @throws \Exception
     */
    public function testInfoToMonoLog()
    {
        $this->_logger = $this->_setUpMonoLogger();
        $this->_logger->info('info message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'info message [priority: 200]');
    }

    /**
     * Test LoggerWrapper::notice to custom log
     * @throws \Exception
     */
    public function testNoticeToCustomLog()
    {
        $this->_logger = $this->_setUpCustomLogger();
        $this->_logger->notice('notice message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'notice message [priority: 5]');
    }

    /**
     * Test LoggerWrapper::notice to mono log
     * @throws \Exception
     */
    public function testNoticeToMonoLog()
    {
        $this->_logger = $this->_setUpMonoLogger();
        $this->_logger->notice('notice message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'notice message [priority: 250]');
    }

    /**
     * Test LoggerWrapper::warning to custom log
     * @throws \Exception
     */
    public function testWarningToCustomLog()
    {
        $this->_logger = $this->_setUpCustomLogger();
        $this->_logger->warning('warning message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'warning message [priority: 4]');
    }

    /**
     * Test LoggerWrapper::warning to mono log
     * @throws \Exception
     */
    public function testWarningToMonoLog()
    {
        $this->_logger = $this->_setUpMonoLogger();
        $this->_logger->warning('warning message');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'warning message [priority: 300]');
    }

    /**
     * Test LoggerWrapper:: logOrDieOnDevOnToCustLogDev on not-dev with log as error = true
     * @throws \Exception
     */
    public function testLogOrDieOnDevToCustLogNotDevLogAsError()
    {
        $this->_logger = $this->_setUpCustomLogger();
        $result = $this->_logger->logOrDieOnDev('test message', true, 'result', 'category_1');
        $this->assertStringEqualsFile($this->_pathToLogfile, 'test message [priority: 3] [options: category_1]');
        $this->assertSame($result, 'result');
    }

    /**
     * Test LoggerWrapper:: logOrDieOnDevOnToCustLogDev on not-dev with log as error = false
     * @throws \Exception
     */
    public function testLogOrDieOnDevToCustLogNotDevLogNotAsError()
    {
        $this->_logger = $this->_setUpCustomLogger();
        $result = $this->_logger->logOrDieOnDev('test message', false, 'result', 'category_1');
        $this->assertFileNotExists($this->_pathToLogfile);
        $this->assertSame($result, 'result');
    }

//    /**
//     * Test LoggerWrapper:: logOrDieOnDevOnToCustLogDev on Dev => not testable, because script dies!
//     */
//    public function testLogOrDieOnDevOnToCustLogDev()
//    {
//        $this->_logger = $this->_setUpCustomLogger(true);
//        $this->_logger->logOrDieOnDev('test message', true, 'test', 'category_1');
//    }

    /**
     * @param bool $isDevEnvironment
     * @return LoggerWrapper
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    protected function _setUpCustomLogger(bool $isDevEnvironment = false)
    {
        return new LoggerWrapper(
            $this->getMockBuilder(CustomLogMock::class)
                ->setConstructorArgs([$this->_pathToLogfile, $isDevEnvironment])
                ->setMethods(null)
                ->getMock(),
            $isDevEnvironment
        );
    }

    /**
     * @param bool $hasStreamHandlers
     * @return LoggerWrapper
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    protected function _setUpMonoLogger(bool $hasStreamHandlers = true)
    {
        return new LoggerWrapper(
            $this->getMockBuilder(MonologMock::class)
                ->setConstructorArgs([$this->_pathToLogfile])
                ->setMethods(null)
                ->getMock()
        );
    }
}
