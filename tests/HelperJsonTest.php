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

use Gyselroth\Helper\HelperJson;

class HelperJsonTest extends HelperTestCase
{
    private $array = [],
            $jsonEncoded;

    protected function setUp()
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
        $this->assertEmpty(HelperJson::decode(''));
    }

    /**
     * @throws \Exception
     */
    public function testDecodeArray(): void
    {
        $this->assertEquals($this->array, HelperJson::decode($this->jsonEncoded));
    }

    /**
     * @throws \Exception
     */
    public function testDecodeObject(): void
    {
        $object = (object) $this->array;
        $this->assertEquals($object, HelperJson::decode($this->jsonEncoded, 0));
    }

    /**
     * @throws \Exception
     */
    public function testDecodeInvalid(): void
    {
        $this->assertEmpty(HelperJson::decode("{'key':'value'}"));
    }

    public function testEnsureIsJsonTrue(): void
    {
        $this->assertSame($this->jsonEncoded, HelperJson::ensureIsJson($this->jsonEncoded));
    }

    public function testEnsureIsJsonFalse(): void
    {
        $this->assertSame('HTML Code (expected JSON)', HelperJson::ensureIsJson('<!DOCTYPE'));
    }
}
