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

use Gyselroth\Helper\HelperJson;

class HelperJsonTest extends HelperTestCase
{
    private $array = [];
    private $jsonEncoded;

    protected function setUp(): void
    {
        $this->array = [
            'key1' => ['subvalue1', 'subvalue2'],
            'value1',
            'value2'
        ];
        $this->jsonEncoded = '{"key1":["subvalue1","subvalue2"],"0":"value1","1":"value2"}';
    }

    /**
     * @throws \Exception
     */
    public function testDecodeEmpty(): void
    {
        self::assertEmpty(HelperJson::decode(''));
    }

    /**
     * @throws \Exception
     */
    public function testDecodeArray(): void
    {
        self::assertEquals($this->array, HelperJson::decode($this->jsonEncoded));
    }

    /**
     * @throws \Exception
     */
    public function testDecodeObject(): void
    {
        $object = (object) $this->array;

        self::assertEquals($object, HelperJson::decode($this->jsonEncoded, 0));
    }

    /**
     * @throws \Exception
     */
    public function testDecodeInvalid(): void
    {
        self::assertEmpty(HelperJson::decode("{'key':'value'}"));
    }

    public function testEnsureIsJsonTrue(): void
    {
        self::assertSame($this->jsonEncoded, HelperJson::ensureIsJson($this->jsonEncoded));
    }

    public function testEnsureIsJsonFalse(): void
    {
        self::assertSame('HTML Code (expected JSON)', HelperJson::ensureIsJson('<!DOCTYPE'));
    }
}
