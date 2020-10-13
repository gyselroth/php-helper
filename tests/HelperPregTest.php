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

use Gyselroth\Helper\Exception\PregExceptionEmptyExpression;
use Gyselroth\Helper\HelperPreg;
use Gyselroth\Helper\HelperString;

class HelperPregTest extends HelperTestCase
{
    /**
     * Test: HelperPreg::removeNonNumericChars
     */
    public function testRemoveNonNumericChars(): void
    {
        $this->assertEquals(
            '1213421231.',
            HelperPreg::removeNonNumericChars('12 1Lorem ipsum dolor sit a3met421231.'));

        $this->assertEquals(
            1213421231,
            HelperPreg::removeNonNumericChars('12 1Lorem ipsum dolor sit a3met421231.', true));
    }

    /**
     * Test: HelperString::startsNumeric
     */
    public function testStartsNumeric(): void
    {
        $this->assertNotTrue(HelperPreg::startsNumeric('m32'));

//        $this->assertTrue(HelperString::startsNumeric('4m2'));

        $this->assertNotTrue(HelperPreg::startsNumeric(' 3m'));
        $this->assertNotTrue(HelperPreg::startsNumeric('.3m'));
        $this->assertNotTrue(HelperPreg::startsNumeric('-3m'));
    }

    public function testPregRemoveAllBetween(): void
    {
        $res = HelperPreg::pregRemoveBetween('lalala jo brolo, bro chacka lacka lacka bro luck!', '/\sbro\s/i', '/la/i');
        $this->assertEquals('lalala jo brolo,cka lacka bro luck!', $res);

        $res = HelperPreg::pregRemoveBetween('lalala jo brolo, bro chacka lacka lacka bro luck!', '/\sbro\s/i', '/la[a-z\s]*l/i');
        $this->assertEquals('lalala jo brolo,uck!', $res);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function testPregStrPosFoundWithOffset(): void
    {
        $offset = HelperPreg::pregStrPos('hey hey jo hey chacka lacka', '/hey/i', 3);
        $this->assertEquals(4, $offset);

        $offset = HelperPreg::pregStrPos('hey hey jo hey chacka lacka', '/hey/i', 8);
        $this->assertEquals(11, $offset);

        $offset = HelperPreg::pregStrPos('hey hey jo hey chacka lacka', '/acka/i', 18);
        $this->assertEquals(23, $offset);
    }

    public function testPregStrPosNotFoundEmptyPatternNoOffset(): void
    {
        $caught = false;
        try {
            HelperPreg::pregStrPos('', '');
        } catch (PregExceptionEmptyExpression $e) {
            $caught = true;
        }

        $this->assertTrue($caught);
    }

    /**
     * @throws PregExceptionEmptyExpression
     */
    public function testPregStrPosNotFoundEmptyHaystackNoOffset(): void
    {
        $offset = HelperPreg::pregStrPos('', '/xxx[a-z]+/i');
        $this->assertEquals(-1, $offset);
    }

    /**
     * @throws PregExceptionEmptyExpression
     */
    public function testPregStrPosNotFoundNoOffset(): void
    {
        $offset = HelperPreg::pregStrPos('pess sunt planetas 123 de neuter ratione.', '/xxx[a-z]+/i');
        $this->assertEquals(-1, $offset);
    }

    /**
     * @throws PregExceptionEmptyExpression
     */
    public function testPregStrPosFoundNoOffset(): void
    {
        $offset = HelperPreg::pregStrPos('pess sunt planetas 123 de neuter ratione.', '/pla[a-z]+\s\d+/i');
        $this->assertEquals(10, $offset);

        $offset = HelperPreg::pregStrPos('pess sunt planetas 123 de neuter ratione.', '/\d+/i');
        $this->assertEquals(19, $offset);
    }
}
