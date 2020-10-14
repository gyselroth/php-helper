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

use Exception;
use Gyselroth\Helper\Exception\ReflectionException;
use Gyselroth\Helper\Exception\ReflectionExceptionInvalidType;
use Gyselroth\Helper\Exception\ReflectionExceptionUndefinedFunction;
use Gyselroth\Helper\HelperReflection;
use Gyselroth\HelperLog\Exception\LoggerException;
use PHPUnit\Framework\Constraint\IsType;
use stdClass;

class HelperReflectionTest extends HelperTestCase
{
    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     * @throws \Gyselroth\Helper\Exception\ReflectionExceptionInvalidType
     */
    public function testGetTypeCasted(): void
    {
        $array = [];
        $object = new stdClass();
        $bool = true;
        $float = (float) 1.0;
        $int = (integer) 1;
        $intShort = (int) 1;
        $string = 'string';

        self::assertThat(
            HelperReflection::getTypeCasted('1', 'bool'),
            new IsType('bool')
        );

        self::assertThat(
            HelperReflection::getTypeCasted('1', 'array'),
            new IsType('array')
        );

        self::assertThat(
            HelperReflection::getTypeCasted('1', 'int'),
            new IsType('int')
        );

        self::assertThat(
            HelperReflection::getTypeCasted('1', 'integer'),
            new IsType('integer')
        );

        self::assertThat(
            HelperReflection::getTypeCasted('1', 'string'),
            new IsType('string')
        );
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
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
        self::assertThat(
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
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     * @throws \Gyselroth\Helper\Exception\ReflectionException
     */
    public function testEnsureIsClass(): void
    {
        self::assertTrue(HelperReflection::ensureIsClass('Gyselroth\Helper\HelperReflection'));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
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
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
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
        self::markTestSkipped('Used function HelperFile::scanDirRecursive() already tested in HelperFileTest');
    }

    public function testGetActionsFromControllerFile(): void
    {
        self::markTestSkipped('Used function HelperFile::scanDirRecursive() already tested in HelperFileTest');

//        $actions = [
//            'index',
//            'dailySchedule',
//            'ajaxGetTimegridHeader',
//            'ajaxGetDailySchedule'
//        ];
//        $path = __DIR__ . '/Fixtures/data/files/TimetableScheduleController.php';
//        self::assertEquals($actions, HelperReflection::getActionsFromControllerFile($path));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\ReflectionExceptionUndefinedFunction
     * @throws \Exception
     */
    public function testCallUserFunctionFunction(): void
    {
        self::assertSame(
            'string',
            HelperReflection::callUserFunction('\print_r', 'string', true)
        );
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     * @throws \Gyselroth\Helper\Exception\ReflectionExceptionUndefinedFunction
     */
    public function testCallUserFunctionMethod(): void
    {
        self::assertTrue(
            HelperReflection::callUserFunction(
                '\Gyselroth\Helper\HelperString::startsWith',
                'string',
                's'
            )
        );
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
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
        self::assertTrue(
            HelperReflection::callUserFunctionArray(
                '\Gyselroth\Helper\HelperString::startsWith',
                ['string', 's']
            )
        );
    }

    /**
     * @throws \Gyselroth\Helper\Exception\ReflectionException
     * @expectedException \Gyselroth\Helper\Exception\ReflectionException
     */
    public function testCallUserFunctionArrayException(): void
    {
        HelperReflection::callUserFunctionArray(
            '\Gyselroth\Helper\HelperReflection::nonExistingFunction12345',
            ['argument1', 'argument2']
        );
    }

    public function testIsFunctionReferenceFunction(): void
    {
        self::assertTrue(HelperReflection::isFunctionReference('\print_r'));
    }

    public function testIsFunctionReferenceMethod(): void
    {
        self::assertTrue(HelperReflection::isFunctionReference('\Gyselroth\Helper\HelperString::startsWith'));
    }

    public function testGetConstantFromPhpClassFile(): void
    {
        self::markTestSkipped('Method not used.');
    }

    public function testGetCallingMethodName(): void
    {
        $callingMethodNameWithClass = 'Tests\HelperReflectionTest::testGetCallingMethodName';
        $callingMethodName = 'testGetCallingMethodName';

        self::assertSame($callingMethodNameWithClass, HelperReflection::getCallingMethodName());

        self::assertSame($callingMethodName, HelperReflection::getCallingMethodName(false));
    }

    public function testGetCallee(): void
    {
        self::assertSame('PHPUnit\Framework\TestCase::runTest()', HelperReflection::getCallee());
    }
}
