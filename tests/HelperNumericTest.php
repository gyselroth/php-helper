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

use Gyselroth\Helper\HelperNumeric;

class HelperNumericTest extends HelperTestCase
{
    public function testFormatAmountDigits(): void
    {
        $this->assertEquals('01', HelperNumeric::formatAmountDigits(1, 2));

        $this->assertEquals(HelperNumeric::formatAmountDigits(1, 1), '1');
        $this->assertEquals(HelperNumeric::formatAmountDigits(1, 2), '01');
        $this->assertEquals(HelperNumeric::formatAmountDigits(1, 3), '001');
        $this->assertEquals(HelperNumeric::formatAmountDigits(100, 1), '100');
    }

    /**
     * Test: HelperNumeric::intImplode
     */
    public function testIntImplode(): void
    {
        $this->assertSame(HelperNumeric::intImplode([3, 5, 1, 3, 4, 2, 2], '-', false, true), '3-5-1-4-2');
        $this->assertSame(HelperNumeric::intImplode([3, 5, 1, 3, 4, 2.3, 2.7]), '1,2,2,3,4,5');
    }

    /**
     * Test: HelperNumeric::intExplode
     */
    public function testIntExplode(): void
    {
        $this->assertSame(json_encode(HelperNumeric::intExplode('3-5-1-4-2-2', '-')), '[3,5,1,4,2,2]');
//        $this->assertSame(json_encode(HelperNumeric::intExplode(null)), '[]');
        $this->assertSame(json_encode(HelperNumeric::intExplode('')), '[0]');
    }

    /**
     * Test: HelperNumeric::floatExplode
     */
    public function testFloatExplode(): void
    {
        $this->assertSame(json_encode(HelperNumeric::floatExplode('3.3-5-1.5-4-2-2', '-')), '[3.3,5,1.5,4,2,2]');
        $this->assertSame(json_encode(HelperNumeric::floatExplode('3.3-5-1.5-4-2-2', '.')), '[3,3,5]');
//        $this->assertSame(json_encode(HelperNumeric::floatExplode(null, '.', false)), '[]');
    }

    /**
     * Test: HelperNumeric::calcBytesSize
     */
    public function testCalcBytesSize(): void
    {
        $this->assertSame(json_encode(HelperNumeric::calcBytesSize(234)), '{"size":234,"unit":"B"}');
        $this->assertSame(json_encode(HelperNumeric::calcBytesSize(1150)), '{"size":1.1,"unit":"KB"}');
        $this->assertSame(json_encode(HelperNumeric::calcBytesSize(4500000)), '{"size":4.4,"unit":"MB"}');
    }
}
