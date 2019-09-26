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

use Gyselroth\Helper\Exception\PregExceptionEmptyExpression;
use Gyselroth\Helper\HelperString;

class HelperStringTest extends HelperTestCase
{
    public function testGetStringBetween()
    {
        $this->assertEquals('miny',         HelperString::getStringBetween('eeny meeny miny moe', 'meeny', 'moe'));

        $this->assertSame('quick',          HelperString::getStringBetween('the quick brown fox', 'the', 'brown', true));
        $this->assertSame('quick brown',    HelperString::getStringBetween('the quick brown fox', 'the', 'fox', true));
        $this->assertSame(' quick ',        HelperString::getStringBetween('the quick brown fox', 'the', 'brown', false));
        $this->assertSame('',               HelperString::getStringBetween('the quick brown fox', 'brown', '', true));
        $this->assertSame('',               HelperString::getStringBetween('the quick brown fox', '', 'quick', true));
        $this->assertSame('',               HelperString::getStringBetween('the quick brown fox', '', '', true));
    }

    /**
     * Test: HelperString::startsWith
     */
    public function testStartsWith(): void
    {
        $this->assertTrue(HelperString::startsWith('abcdcba', ''));
        $this->assertTrue(HelperString::startsWith('abcdcba', 'a'));

        $this->assertNotTrue(HelperString::startsWith('abcdcba', 'b'));

//        $this->assertTrue(HelperString::startsWith('abcdcba', ['b', 'c', 'a', 'd', 'a']));

//        $this->assertNotTrue(HelperString::startsWith('abcdcba', ['b', 'c', 'f', 'd', 'g']));
    }

    /**
     * Test: HelperString::endsWith
     */
    public function testEndsWith(): void
    {
        $this->assertTrue(HelperString::endsWith('abcdcba', ''));
        $this->assertTrue(HelperString::endsWith('abcdcba', 'a'));

        $this->assertNotTrue(HelperString::endsWith('abcdcba', 'b'));

        $this->assertTrue(HelperString::endsWith('abcdcba', ['b', 'c', 'a', 'd', 'a']));

        $this->assertNotTrue(HelperString::endsWith('abcdcba', ['b', 'c', 'f', 'd', 'g']));
    }

    /**
     * @throws PregExceptionEmptyExpression
     */
    public function testPregStrPosFoundNoOffset(): void
    {
        $offset = HelperString::pregStrPos('pess sunt planetas 123 de neuter ratione.', '/pla[a-z]+\s\d+/i');
        $this->assertEquals(10, $offset);

        $offset = HelperString::pregStrPos('pess sunt planetas 123 de neuter ratione.', '/\d+/i');
        $this->assertEquals(19, $offset);
    }

    /**
     * @throws PregExceptionEmptyExpression
     */
    public function testPregStrPosNotFoundNoOffset(): void
    {
        $offset = HelperString::pregStrPos('pess sunt planetas 123 de neuter ratione.', '/xxx[a-z]+/i');
        $this->assertEquals(-1, $offset);
    }

    /**
     * @throws PregExceptionEmptyExpression
     */
    public function testPregStrPosNotFoundEmptyHaystackNoOffset(): void
    {
        $offset = HelperString::pregStrPos('', '/xxx[a-z]+/i');
        $this->assertEquals(-1, $offset);
    }

    public function testPregStrPosNotFoundEmptyPatternNoOffset(): void
    {
        $caught = false;
        try {
            HelperString::pregStrPos('', '');
        } catch (PregExceptionEmptyExpression $e) {
            $caught = true;
        }

        $this->assertTrue($caught);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function testPregStrPosFoundWithOffset(): void
    {
        $offset = HelperString::pregStrPos('hey hey jo hey chacka lacka', '/hey/i', 3);
        $this->assertEquals(4, $offset);

        $offset = HelperString::pregStrPos('hey hey jo hey chacka lacka', '/hey/i', 8);
        $this->assertEquals(11, $offset);

        $offset = HelperString::pregStrPos('hey hey jo hey chacka lacka', '/acka/i', 18);
        $this->assertEquals(23, $offset);
    }

    /**
     * Test: HelperString::replaceFirst
     */
    public function testReplaceFirst(): void
    {
        $this->assertEquals(
            'Lorem ipsum dolor sit amet,  sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::replaceFirst('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 'consetetur'));

        $this->assertEquals(
            'Lorem ipsum dolor sit amet, e sadipscing elitr, sed diam nonumy conseteture eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::replaceFirst('Lorem ipsum dolor sit amet, conseteture sadipscing elitr, sed diam nonumy conseteture eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 'consetetur'));

        $this->assertEquals(
            'Lorem ipsum dolor sit amet, test sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::replaceFirst('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 'consetetur', 'test'));
    }

    /**
     * Test: HelperString::replaceLast
     */
    public function testReplaceLast(): void
    {
        $this->assertEquals(
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy  eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::replaceLast('consetetur', '', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'));

        $this->assertEquals(
            'Lorem ipsum dolor sit amet, conseteture sadipscing elitr, sed diam nonumy e eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::replaceLast('consetetur', '', 'Lorem ipsum dolor sit amet, conseteture sadipscing elitr, sed diam nonumy conseteture eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'));

        $this->assertEquals(
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy test eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::replaceLast('consetetur', 'test', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'));
    }

    /**
     * Test: HelperString::wrap
     */
    public function testWrap(): void
    {
        $this->assertEquals(
            'leftside->TOBEWRAPPED<-rightside',
            HelperString::wrap('TOBEWRAPPED', 'leftside->', '<-rightside', false));

        $this->assertEquals(
            'leftside->leftside->TOBEWRAPPED<-rightside<-rightside',
            HelperString::wrap('leftside->TOBEWRAPPED<-rightside', 'leftside->', '<-rightside', false));

        $this->assertEquals(
            'leftside->TOBEWRAPPED<-rightside',
            HelperString::wrap('TOBEWRAPPED', 'leftside->', '<-rightside'));

        $this->assertEquals(
            'leftside->TOBEWRAPPED<-rightside',
            HelperString::wrap('leftside->TOBEWRAPPED<-rightside', 'leftside->','<-rightside'));
    }

    public function testRemoveBetweenLhsNotGiven(): void
    {
        $this->assertEquals('brave new world', HelperString::removeAllBetween('brave new world', 'happy', 'earth'));
    }
    public function testRemoveBetweenRhsNotGiven(): void
    {
        $this->assertEquals('brave new world', HelperString::removeAllBetween('brave new world', 'brave', 'earth'));
    }
    public function testRemoveBetweenRemoveDelimiters(): void
    {
        $this->assertEquals('its a over there', HelperString::removeAllBetween('its a brave new world over there', 'brave', ' world '));
    }
    public function testRemoveBetweenKeepDelimiters(): void
    {
        $this->assertEquals('its a brave world over there', HelperString::removeAllBetween('its a brave new world over there', 'brave', ' world', false));
    }

    public function testPregRemoveAllBetween(): void
    {
        $res = HelperString::pregRemoveBetween('lalala jo brolo, bro chacka lacka lacka bro luck!', '/\sbro\s/i', '/la/i');
        $this->assertEquals('lalala jo brolo,cka lacka bro luck!', $res);

        $res = HelperString::pregRemoveBetween('lalala jo brolo, bro chacka lacka lacka bro luck!', '/\sbro\s/i', '/la[a-z\s]*l/i');
        $this->assertEquals('lalala jo brolo,uck!', $res);
    }

    /**
     * Test: HelperString::unwrap
     */
    public function testUnwrap(): void
    {
        $this->assertEquals('TOBEUNWRAPPED', HelperString::unwrap('leftside->TOBEUNWRAPPED<-rightside', 'leftside->', '<-rightside'));
        $this->assertEquals('leftside->TOBEUNWRAPPED<-rightside', HelperString::unwrap('leftside->TOBEUNWRAPPED<-rightside', 'rightside->', '<-leftside'));
    }

    /**
     * Test: HelperString::removeAllBefore
     */
    public function testRemoveAllBefore(): void
    {
        $this->assertEquals(
            'consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::removeAllBefore('consetetur', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'));

        $this->assertEquals(
            'consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::removeAllBefore('consetetur', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 30));

//        $this->assertEquals(
//            ' ut labore et dolore magna aliquyam erat, sed diam voluptua.',
//            HelperString::removeAllBefore('invidunt', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', null, true));

        $this->assertEquals(
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::removeAllBefore('consetetur', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 500));
    }

    /**
     * Test: HelperString::removeAllAfter
     */
    public function testRemoveAllAfter(): void
    {
        $this->assertEquals(
            'Lorem ipsum dolor sit amet, consetetur',
            HelperString::removeAllAfter('consetetur', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'));

        $this->assertEquals(
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur',
            HelperString::removeAllAfter('consetetur', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 30));

//        $this->assertEquals(
//            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor '
//            HelperString::removeAllAfter('invidunt', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', null, true));

        $this->assertEquals(
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::removeAllAfter('consetetur', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 500));
    }

    /**
     * Test: HelperString::startsNumeric
     */
    public function testStartsNumeric(): void
    {
        $this->assertNotTrue(HelperString::startsNumeric('m32'));

//        $this->assertTrue(HelperString::startsNumeric('4m2'));

        $this->assertNotTrue(HelperString::startsNumeric(' 3m'));
        $this->assertNotTrue(HelperString::startsNumeric('.3m'));
        $this->assertNotTrue(HelperString::startsNumeric('-3m'));
    }

    /**
     * Test: HelperString::strposMultiple
     */
    public function testStrPosMultiple(): void
    {
        $this->assertEquals(
            json_encode(['consectetur' => 0, 'magna' => 127, ' ' => 5]),
            json_encode(HelperString::strPosConsecutive('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', ['consectetur', 'magna', ' '])));

        $this->assertEquals(
            json_encode(false),
            json_encode(HelperString::strPosConsecutive('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', ['consecteture', 'magnam', 1])));
    }

    /**
     * Test: HelperString::removeNumericChars
     */
    public function testRemoveNumericChars(): void
    {
        $this->assertEquals(
            'Lorem ipsum dolor sit amet.',
            HelperString::removeNumericChars('12 1Lorem ipsum dolor sit a3met421231.'));

        $this->assertEquals(
            ' Lorem ipsum dolor sit amet.',
            HelperString::removeNumericChars('12 1Lorem ipsum dolor sit a3met421231.', false));
    }

    /**
     * Test: HelperString::removeNonNumericChars
     */
    public function testRemoveNonNumericChars(): void
    {
        $this->assertEquals(
            '1213421231.',
            HelperString::removeNonNumericChars('12 1Lorem ipsum dolor sit a3met421231.'));

        $this->assertEquals(
            1213421231,
            HelperString::removeNonNumericChars('12 1Lorem ipsum dolor sit a3met421231.', true));
    }

    /**
     * Test: HelperString::formatJsonCompatible
     */
    public function testFormatJsonCompatible(): void
    {
        $this->assertEquals(
            'Lorem "ipsum" dolor sit amet, consetetur sadipscing elitr. Sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::formatJsonCompatible("Lorem 'ipsum' dolor sit amet, consetetur sadipscing elitr. \nSed diam nonumy consetetur eirmod tempor invidunt ut labore\r et dolore magna aliquyam erat, sed diam voluptua."));
    }

    /**
     * Test: HelperString::isUtf8
     */
    public function testIsUtf8(): void
    {
        $this->assertTrue(HelperString::isUtf8('äöü€'));

        $this->assertNotTrue(HelperString::isUtf8('aouE'));
    }

    /**
     * Test: HelperString::reduceCharRepetitions
     */
    public function testReduceCharRepetitions(): void
    {
        $this->assertEquals(
            'Lorem ipsum dolor sit aAmet, consetetur sssssadipscing elitr, sed diam nonumyBb consetetur eirmod tempor invidunt.',
            HelperString::reduceCharRepetitions('Lorem ipsum dolor sit aAAAmet, consetetur sssssadipscing elitr, sed diam nonumyBbBb consetetur eirmod tempor invidunt.....', ['.', 'A', 'Bb']));

        $this->assertEquals(
            'Lorem ipsum dolor sit amet, consetetur sadipscing eliTr, sed diam nonumy consetetur eirmod tempor invidunt.',
            HelperString::reduceCharRepetitions('Lorem ipsum dolor sit amet, consetetur sadipscing eliTTTTTTr, sed diam nonumy consetetur eirmod tempor invidunt.', 'T'));
    }

    /**
     * Test: HelperString::toCamelCase
     */
    public function testToCamelCase(): void
    {
        $this->assertEquals('toCamel-Case', HelperString::toCamelCase('to-camel--case'));
        $this->assertEquals('ToCamel-Case', HelperString::toCamelCase('to-camel--case', true));
    }

    /**
     * Test: HelperString::getPathFromCamelCase
     */
    public function testGetPathFromCamelCase(): void
    {
        $this->assertEquals('to-camel--case', HelperString::getPathFromCamelCase('toCamel-Case'));
        $this->assertEquals('to=camel-=case', HelperString::getPathFromCamelCase('toCamel-Case', '='));
    }

    /**
     * Test: HelperString::containsAnyOf
     */
    public function testContainsAnyOf(): void
    {
        $this->assertTrue(HelperString::containsAnyOf('a', 'a'));
        $this->assertTrue(HelperString::containsAnyOf('abc', 'b'));
        $this->assertTrue(HelperString::containsAnyOf('abc', 'xyza'));

        $this->assertNotTrue(HelperString::containsAnyOf('', 'd'));
        $this->assertNotTrue(HelperString::containsAnyOf('abc', 'd'));
    }

    /**
     * Test: HelperString::pregMatchAllWithOffsets
     */
    public function testPregMatchAllWithOffsets(): void
    {
        $this->assertEquals(
            json_encode([2 => 'a', 10 => 'a', 21 => 'a', 25 => 'a', 30 => 'a', 35 => 'a', 40 => 'a']),
            json_encode(HelperString::preg_match_all_with_offsets('/a/', 'Fraticinidas velum, tanquam flavum adiurator.')));

        $this->assertEquals(
            json_encode([11 => 's9', 14 => 'v9', 42 => 'm9']),
            json_encode(HelperString::preg_match_all_with_offsets('/[a-z]9/', 'Fraticinidas9 v9 el-1 um2, tanquam 9 flavum9 adiurator39.')));

        $this->assertEquals(
            json_encode([1 => 'raticinidas9', 14 => 'v9', 37 => 'flavum9']),
            json_encode(HelperString::preg_match_all_with_offsets('/[a-z]+9/', 'Fraticinidas9 v9 el-1 um2, tanquam 9 flavum9 adiurator39.')));
    }

    /**
     * Test: HelperString::getRandomString
     * @throws \InvalidArgumentException
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Exception
     */
    public function testGetRandomString(): void
    {
        $this->assertEquals(12, \strlen(HelperString::getRandomString(12)));
        $this->assertEquals(1, preg_match('/^(?=.*?[a-z]+)(?=.*?[\d]+)/', HelperString::getRandomString()));
        $this->assertEquals(1, preg_match('/^(?=.*?[a-z]+)(?=.*?[A-Z]+)(?=.*?[\.\-\?&\$]+)/', HelperString::getRandomString(12, true, true, false, '.-?&$')));
    }

    /**
     * Test: HelperString::getRandomLetter
     */
    public function testGetRandomLetter(): void
    {
        $this->assertEquals(1, preg_match('/[a-z]/', HelperString::getRandomLetter()));
        $this->assertEquals(1, preg_match('/[A-E]+|[0-3]+/', HelperString::getRandomLetter(true, 'acbed0123')));
    }

    /**
     * Test: HelperString::toAlpha
     */
    public function testToAlpha(): void
    {
        $this->assertEquals('d', HelperString::toAlpha(3));
        $this->assertEquals('bym', HelperString::toAlpha(2012 + 2));
    }

    /**
     * Test: HelperString::urlsafeB64encode
     */
    public function testUrlSafeB64encode(): void
    {
        $this->assertEquals('aGFsbG8.', HelperString::urlSafeB64encode('hallo'));
    }

    /**
     * Test: HelperString::urlSafeB64Decode
     */
    public function testUrlSafeB64Decode(): void
    {
        $this->assertEquals('hallo', HelperString::urlSafeB64Decode('aGFsbG8.'));
        $this->assertEquals('hallo', HelperString::urlSafeB64Decode('aGFsbG8..'));
    }

    /**
     * Test: HelperString::compareValuesByComparisonOperators
     */
    public function testCompareValuesByComparisonOperators(): void
    {
        $this->assertNotTrue(HelperString::compareValuesByComparisonOperators('test', 'Test', 'eq'));

        $this->assertTrue(HelperString::compareValuesByComparisonOperators('test', 0, 'eq'));

        $this->assertNotTrue(HelperString::compareValuesByComparisonOperators('123', 0, 'eq'));

        $this->assertTrue(HelperString::compareValuesByComparisonOperators('test', 'test2', 'lt'));
        $this->assertTrue(HelperString::compareValuesByComparisonOperators('0123', 123, 'eq'));

        $this->assertNotTrue(HelperString::compareValuesByComparisonOperators('0123', 123, 'eq', true));

        $this->assertTrue(HelperString::compareValuesByComparisonOperators('25H', 25, 'eq'));
        $this->assertTrue(HelperString::compareValuesByComparisonOperators(true, 0, 'gt'));
        $this->assertTrue(HelperString::compareValuesByComparisonOperators(24, 22));
    }

    /**
     * Test: HelperString::mb_str_split
     */
    public function testMb_str_split(): void
    {
        $this->assertEquals(['t','3',' ','_','e','0','s','t'], HelperString::mb_str_split('t3 _e0st'));
    }

    public function testTranslate(): void
    {
        $this->assertSame('test', HelperString::translate('test'));
        $this->assertSame('This is 1 test', HelperString::translate('This is %d %s', [1, 'test']));
    }

    public function testTranslatePlural(): void
    {
        $this->assertSame('Single', HelperString::translatePlural('Single', 'Plural', -1));
        $this->assertSame('Single', HelperString::translatePlural('Single', 'Plural', 1));
        $this->assertSame('Plural', HelperString::translatePlural('Single', 'Plural', 0));
        $this->assertSame('Plural', HelperString::translatePlural('Single', 'Plural', 2));
    }
}
