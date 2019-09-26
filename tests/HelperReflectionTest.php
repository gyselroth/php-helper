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

use Gyselroth\Helper\HelperReflection;
use PHPUnit\Framework\Constraint\IsType;

class HelperReflectionTest extends HelperTestCase
{
    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Gyselroth\Helper\Exception\ReflectionExceptionInvalidType
     */
    public function testGetTypeCasted(): void
    {
        $array = [];
        $object = new \stdClass();
        $bool = true;
        $float = (float) 1.0;
        $int = (integer) 1;
        $intShort = (int) 1;
        $string = 'string';

        $this->assertThat(
            HelperReflection::getTypeCasted('1', 'bool'),
            new IsType('bool')
        );
        $this->assertThat(
            HelperReflection::getTypeCasted('1', 'array'),
            new IsType('array')
        );
        $this->assertThat(
            HelperReflection::getTypeCasted('1', 'int'),
            new IsType('int')
        );
        $this->assertThat(
            HelperReflection::getTypeCasted('1', 'integer'),
            new IsType('integer')
        );
        $this->assertThat(
            HelperReflection::getTypeCasted('1', 'string'),
            new IsType('string')
        );
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Gyselroth\Helper\Exception\ReflectionExceptionInvalidType
     * @expectedException \Gyselroth\Helper\Exception\ReflectionExceptionInvalidType
     */
    public function testGetTypeCastedException(): void
    {
        HelperReflection::getTypeCasted('1', 'object');
    }

    /**
     * @throws \Gyselroth\Helper\Exception\ReflectionException
     */
    public function testConstructObject(): void
    {
        $this->assertThat(
            HelperReflection::constructObject('Gyselroth\Helper\HelperReflection'),
            new IsType('object')
        );
    }

    /**
     * @throws \Gyselroth\Helper\Exception\ReflectionException
     * @expectedException \Gyselroth\Helper\Exception\ReflectionException
     */
    public function testConstructObjectException(): void
    {
        HelperReflection::constructObject('NotAClass12345');
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Gyselroth\Helper\Exception\ReflectionException
     */
    public function testEnsureIsClass(): void
    {
        $this->assertTrue(HelperReflection::ensureIsClass('Gyselroth\Helper\HelperReflection'));

    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Gyselroth\Helper\Exception\ReflectionException
     * @expectedException \Gyselroth\Helper\Exception\ReflectionException
     * @expectedExceptionMessage Tried to construct undefined class.
     */
    public function testEnsureIsClassEmptyException(): void
    {
        HelperReflection::ensureIsClass('');
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Gyselroth\Helper\Exception\ReflectionException
     * @expectedException \Gyselroth\Helper\Exception\ReflectionException
     * @expectedExceptionMessage Class not defined: 'NotAClass12345'.
     */
    public function testEnsureIsClassNonExistingException(): void
    {
        HelperReflection::ensureIsClass('NotAClass12345');
    }

    public function testGetControllerFilenames(): void
    {
        $this->markTestSkipped('Used function HelperFile::scanDirRecursive() already tested in HelperFileTest');
    }

    public function testGetActionsFromControllerFile(): void
    {
        $this->markTestSkipped('Used function HelperFile::scanDirRecursive() already tested in HelperFileTest');

//        $actions = [
//            'index',
//            'dailySchedule',
//            'ajaxGetTimegridHeader',
//            'ajaxGetDailySchedule'
//        ];
//        $path = __DIR__ . '/Fixtures/data/files/TimetableScheduleController.php';
//        $this->assertEquals($actions, HelperReflection::getActionsFromControllerFile($path));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\ReflectionExceptionUndefinedFunction
     * @throws \Exception
     */
    public function testCallUserFunctionFunction(): void
    {
        $this->assertSame('string', HelperReflection::callUserFunction('\print_r', 'string', true));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Gyselroth\Helper\Exception\ReflectionExceptionUndefinedFunction
     */
    public function testCallUserFunctionMethod(): void
    {
        $this->assertTrue(HelperReflection::callUserFunction('\Gyselroth\Helper\HelperString::startsWith', 'string', 's'));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Gyselroth\Helper\Exception\ReflectionExceptionUndefinedFunction
     * @expectedException \Gyselroth\Helper\Exception\ReflectionExceptionUndefinedFunction
     */
    public function testCallUserFunctionException(): void
    {
        HelperReflection::callUserFunction('nonExistingFunction12345');
    }

    /**
     * @throws \Gyselroth\Helper\Exception\ReflectionException
     */
    public function testCallUserFunctionArray(): void
    {
        $this->assertTrue(HelperReflection::callUserFunctionArray('\Gyselroth\Helper\HelperString::startsWith', ['string', 's']));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\ReflectionException
     * @expectedException \Gyselroth\Helper\Exception\ReflectionException
     */
    public function testCallUserFunctionArrayException(): void
    {
        HelperReflection::callUserFunctionArray('\Gyselroth\Helper\HelperReflection::nonExistingFunction12345', ['argument1', 'argument2']);
    }

    public function testIsFunctionReferenceFunction(): void
    {
        $this->assertTrue(HelperReflection::isFunctionReference('\print_r'));
    }

    public function testIsFunctionReferenceMethod(): void
    {
        $this->assertTrue(HelperReflection::isFunctionReference('\Gyselroth\Helper\HelperString::startsWith'));
    }

    public function testGetConstantFromPhpClassFile(): void
    {
        $this->markTestSkipped('Method not used.');
    }

    public function testGetCallingMethodName(): void
    {
        $callingMethodNameWithClass = 'Tests\HelperReflectionTest::testGetCallingMethodName';
        $callingMethodName = 'testGetCallingMethodName';
        $this->assertSame($callingMethodNameWithClass, HelperReflection::getCallingMethodName());
        $this->assertSame($callingMethodName, HelperReflection::getCallingMethodName(false));
    }

    public function testGetCallee(): void
    {
        $this->assertSame('PHPUnit\Framework\TestCase::runTest()', HelperReflection::getCallee());
    }
}
