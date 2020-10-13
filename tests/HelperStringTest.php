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
use Gyselroth\Helper\HelperString;

class HelperStringTest extends HelperTestCase
{
    public function testGetStringBetween(): void
    {
        self::assertEquals('miny',         HelperString::getStringBetween('eeny meeny miny moe', 'meeny', 'moe'));

        self::assertSame('quick',          HelperString::getStringBetween('the quick brown fox', 'the', 'brown', true));
        self::assertSame('quick brown',    HelperString::getStringBetween('the quick brown fox', 'the', 'fox', true));
        self::assertSame(' quick ',        HelperString::getStringBetween('the quick brown fox', 'the', 'brown', false));
        self::assertSame('',               HelperString::getStringBetween('the quick brown fox', 'brown', '', true));
        self::assertSame('',               HelperString::getStringBetween('the quick brown fox', '', 'quick', true));
        self::assertSame('',               HelperString::getStringBetween('the quick brown fox', '', '', true));
    }

    /**
     * Test: HelperString::startsWith
     */
    public function testStartsWith(): void
    {
        self::assertTrue(HelperString::startsWith('abcdcba', ''));
        self::assertTrue(HelperString::startsWith('abcdcba', 'a'));

        self::assertNotTrue(HelperString::startsWith('abcdcba', 'b'));

//        self::assertTrue(HelperString::startsWith('abcdcba', ['b', 'c', 'a', 'd', 'a']));

//        self::assertNotTrue(HelperString::startsWith('abcdcba', ['b', 'c', 'f', 'd', 'g']));
    }

    /**
     * Test: HelperString::endsWith
     */
    public function testEndsWith(): void
    {
        self::assertTrue(HelperString::endsWith('abcdcba', ''));
        self::assertTrue(HelperString::endsWith('abcdcba', 'a'));

        self::assertNotTrue(HelperString::endsWith('abcdcba', 'b'));

        self::assertTrue(HelperString::endsWith('abcdcba', ['b', 'c', 'a', 'd', 'a']));

        self::assertNotTrue(HelperString::endsWith('abcdcba', ['b', 'c', 'f', 'd', 'g']));
    }

    /**
     * Test: HelperString::replaceFirst
     */
    public function testReplaceFirst(): void
    {
        self::assertEquals(
            'Lorem ipsum dolor sit amet,  sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::replaceFirst('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 'consetetur'));

        self::assertEquals(
            'Lorem ipsum dolor sit amet, e sadipscing elitr, sed diam nonumy conseteture eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::replaceFirst('Lorem ipsum dolor sit amet, conseteture sadipscing elitr, sed diam nonumy conseteture eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 'consetetur'));

        self::assertEquals(
            'Lorem ipsum dolor sit amet, test sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::replaceFirst('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 'consetetur', 'test'));
    }

    /**
     * Test: HelperString::replaceLast
     */
    public function testReplaceLast(): void
    {
        self::assertEquals(
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy  eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::replaceLast('consetetur', '', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'));

        self::assertEquals(
            'Lorem ipsum dolor sit amet, conseteture sadipscing elitr, sed diam nonumy e eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::replaceLast('consetetur', '', 'Lorem ipsum dolor sit amet, conseteture sadipscing elitr, sed diam nonumy conseteture eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'));

        self::assertEquals(
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy test eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::replaceLast('consetetur', 'test', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'));
    }

    /**
     * Test: HelperString::wrap
     */
    public function testWrap(): void
    {
        self::assertEquals(
            'leftside->TOBEWRAPPED<-rightside',
            HelperString::wrap('TOBEWRAPPED', 'leftside->', '<-rightside', false));

        self::assertEquals(
            'leftside->leftside->TOBEWRAPPED<-rightside<-rightside',
            HelperString::wrap('leftside->TOBEWRAPPED<-rightside', 'leftside->', '<-rightside', false));

        self::assertEquals(
            'leftside->TOBEWRAPPED<-rightside',
            HelperString::wrap('TOBEWRAPPED', 'leftside->', '<-rightside'));

        self::assertEquals(
            'leftside->TOBEWRAPPED<-rightside',
            HelperString::wrap('leftside->TOBEWRAPPED<-rightside', 'leftside->','<-rightside'));
    }

    public function testRemoveBetweenLhsNotGiven(): void
    {
        self::assertEquals('brave new world', HelperString::removeAllBetween('brave new world', 'happy', 'earth'));
    }

    public function testRemoveBetweenRhsNotGiven(): void
    {
        self::assertEquals('brave new world', HelperString::removeAllBetween('brave new world', 'brave', 'earth'));
    }

    public function testRemoveBetweenRemoveDelimiters(): void
    {
        self::assertEquals('its a over there', HelperString::removeAllBetween('its a brave new world over there', 'brave', ' world '));
    }

    public function testRemoveBetweenKeepDelimiters(): void
    {
        self::assertEquals('its a brave world over there', HelperString::removeAllBetween('its a brave new world over there', 'brave', ' world', false));
    }

    /**
     * Test: HelperString::unwrap
     */
    public function testUnwrap(): void
    {
        self::assertEquals('TOBEUNWRAPPED', HelperString::unwrap('leftside->TOBEUNWRAPPED<-rightside', 'leftside->', '<-rightside'));

        self::assertEquals('leftside->TOBEUNWRAPPED<-rightside', HelperString::unwrap('leftside->TOBEUNWRAPPED<-rightside', 'rightside->', '<-leftside'));
    }

    /**
     * Test: HelperString::removeAllBefore
     */
    public function testRemoveAllBefore(): void
    {
        self::assertEquals(
            'consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::removeAllBefore('consetetur', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'));

        self::assertEquals(
            'consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::removeAllBefore('consetetur', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 30));

//        self::assertEquals(
//            ' ut labore et dolore magna aliquyam erat, sed diam voluptua.',
//            HelperString::removeAllBefore('invidunt', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', null, true));

        self::assertEquals(
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::removeAllBefore('consetetur', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 500));
    }

    /**
     * Test: HelperString::removeAllAfter
     */
    public function testRemoveAllAfter(): void
    {
        self::assertEquals(
            'Lorem ipsum dolor sit amet, consetetur',
            HelperString::removeAllAfter('consetetur', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'));

        self::assertEquals(
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur',
            HelperString::removeAllAfter('consetetur', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 30));

//        self::assertEquals(
//            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor '
//            HelperString::removeAllAfter('invidunt', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', null, true));

        self::assertEquals(
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::removeAllAfter('consetetur', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', 500));
    }

    /**
     * Test: HelperString::strposMultiple
     */
    public function testStrPosMultiple(): void
    {
        self::assertEquals(
            json_encode(['consectetur' => 0, 'magna' => 127, ' ' => 5]),
            json_encode(HelperString::strPosConsecutive('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', ['consectetur', 'magna', ' '])));

        self::assertEquals(
            json_encode(false),
            json_encode(HelperString::strPosConsecutive('Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.', ['consecteture', 'magnam', 1])));
    }

    /**
     * Test: HelperString::formatJsonCompatible
     */
    public function testFormatJsonCompatible(): void
    {
        self::assertEquals(
            'Lorem "ipsum" dolor sit amet, consetetur sadipscing elitr. Sed diam nonumy consetetur eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
            HelperString::formatJsonCompatible("Lorem 'ipsum' dolor sit amet, consetetur sadipscing elitr. \nSed diam nonumy consetetur eirmod tempor invidunt ut labore\r et dolore magna aliquyam erat, sed diam voluptua."));
    }

    /**
     * Test: HelperString::isUtf8
     */
    public function testIsUtf8(): void
    {
        self::assertTrue(HelperString::isUtf8('äöü€'));

        self::assertNotTrue(HelperString::isUtf8('aouE'));
    }

    /**
     * Test: HelperString::reduceCharRepetitions
     */
    public function testReduceCharRepetitions(): void
    {
        self::assertEquals(
            'Lorem ipsum dolor sit aAmet, consetetur sssssadipscing elitr, sed diam nonumyBb consetetur eirmod tempor invidunt.',
            HelperString::reduceCharRepetitions('Lorem ipsum dolor sit aAAAmet, consetetur sssssadipscing elitr, sed diam nonumyBbBb consetetur eirmod tempor invidunt.....', ['.', 'A', 'Bb']));

        self::assertEquals(
            'Lorem ipsum dolor sit amet, consetetur sadipscing eliTr, sed diam nonumy consetetur eirmod tempor invidunt.',
            HelperString::reduceCharRepetitions('Lorem ipsum dolor sit amet, consetetur sadipscing eliTTTTTTr, sed diam nonumy consetetur eirmod tempor invidunt.', 'T'));
    }

    /**
     * Test: HelperString::toCamelCase
     */
    public function testToCamelCase(): void
    {
        self::assertEquals('toCamel-Case', HelperString::toCamelCase('to-camel--case'));
        self::assertEquals('ToCamel-Case', HelperString::toCamelCase('to-camel--case', true));
    }

    /**
     * Test: HelperString::getPathFromCamelCase
     */
    public function testGetPathFromCamelCase(): void
    {
        self::assertEquals('to-camel--case', HelperString::getPathFromCamelCase('toCamel-Case'));
        self::assertEquals('to=camel-=case', HelperString::getPathFromCamelCase('toCamel-Case', '='));
    }

    /**
     * Test: HelperString::containsAnyOf
     */
    public function testContainsAnyOf(): void
    {
        self::assertTrue(HelperString::containsAnyOf('a', 'a'));
        self::assertTrue(HelperString::containsAnyOf('abc', 'b'));
        self::assertTrue(HelperString::containsAnyOf('abc', 'xyza'));

        self::assertNotTrue(HelperString::containsAnyOf('', 'd'));
        self::assertNotTrue(HelperString::containsAnyOf('abc', 'd'));
    }

//    /**
//     * Test: HelperString::pregMatchAllWithOffsets
//     */
//    public function testPregMatchAllWithOffsets(): void
//    {
//        self::assertEquals(
//            json_encode([2 => 'a', 10 => 'a', 21 => 'a', 25 => 'a', 30 => 'a', 35 => 'a', 40 => 'a']),
//            json_encode(HelperString::preg_match_all_with_offsets('/a/', 'Fraticinidas velum, tanquam flavum adiurator.')));
//
//        self::assertEquals(
//            json_encode([11 => 's9', 14 => 'v9', 42 => 'm9']),
//            json_encode(HelperString::preg_match_all_with_offsets('/[a-z]9/', 'Fraticinidas9 v9 el-1 um2, tanquam 9 flavum9 adiurator39.')));
//
//        self::assertEquals(
//            json_encode([1 => 'raticinidas9', 14 => 'v9', 37 => 'flavum9']),
//            json_encode(HelperString::preg_match_all_with_offsets('/[a-z]+9/', 'Fraticinidas9 v9 el-1 um2, tanquam 9 flavum9 adiurator39.')));
//    }

    /**
     * Test: HelperString::getRandomString
     * @throws \InvalidArgumentException
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     * @throws \Exception
     */
    public function testGetRandomString(): void
    {
        self::assertEquals(12, \strlen(HelperString::getRandomString(12)));

        self::assertEquals(1, preg_match('/^(?=.*?[a-z]+)(?=.*?[\d]+)/', HelperString::getRandomString()));

        self::assertEquals(1, preg_match('/^(?=.*?[a-z]+)(?=.*?[A-Z]+)(?=.*?[\.\-\?&\$]+)/', HelperString::getRandomString(12, true, true, false, '.-?&$')));
    }

    /**
     * Test: HelperString::getRandomLetter
     */
    public function testGetRandomLetter(): void
    {
        self::assertEquals(1, preg_match('/[a-z]/', HelperString::getRandomLetter()));
        self::assertEquals(1, preg_match('/[A-E]+|[0-3]+/', HelperString::getRandomLetter(true, 'acbed0123')));
    }

    /**
     * Test: HelperString::toAlpha
     */
    public function testToAlpha(): void
    {
        self::assertEquals('d', HelperString::toAlpha(3));
        self::assertEquals('bym', HelperString::toAlpha(2012 + 2));
    }

    /**
     * Test: HelperString::urlsafeB64encode
     */
    public function testUrlSafeB64encode(): void
    {
        self::assertEquals('aGFsbG8.', HelperString::urlSafeB64encode('hallo'));
    }

    /**
     * Test: HelperString::urlSafeB64Decode
     */
    public function testUrlSafeB64Decode(): void
    {
        self::assertEquals('hallo', HelperString::urlSafeB64Decode('aGFsbG8.'));
        self::assertEquals('hallo', HelperString::urlSafeB64Decode('aGFsbG8..'));
    }

    /**
     * Test: HelperString::compareValuesByComparisonOperators
     */
    public function testCompareValuesByComparisonOperators(): void
    {
        self::assertNotTrue(HelperString::compareValuesByComparisonOperators('test', 'Test', 'eq'));

        self::assertTrue(HelperString::compareValuesByComparisonOperators('test', 0, 'eq'));

        self::assertNotTrue(HelperString::compareValuesByComparisonOperators('123', 0, 'eq'));

        self::assertTrue(HelperString::compareValuesByComparisonOperators('test', 'test2', 'lt'));
        self::assertTrue(HelperString::compareValuesByComparisonOperators('0123', 123, 'eq'));

        self::assertNotTrue(HelperString::compareValuesByComparisonOperators('0123', 123, 'eq', true));

        self::assertTrue(HelperString::compareValuesByComparisonOperators('25H', 25, 'eq'));
        self::assertTrue(HelperString::compareValuesByComparisonOperators(true, 0, 'gt'));
        self::assertTrue(HelperString::compareValuesByComparisonOperators(24, 22));
    }

//    /**
//     * Test: HelperString::mb_str_split
//     */
//    public function testMbStrSplit(): void
//    {
//        self::assertEquals(['t','3',' ','_','e','0','s','t'], HelperString::mbStrSplit('t3 _e0st'));
//    }

    public function testTranslate(): void
    {
        self::assertSame('test', HelperString::translate('test'));
        self::assertSame('This is 1 test', HelperString::translate('This is %d %s', [1, 'test']));
    }

    public function testTranslatePlural(): void
    {
        self::assertSame('Single', HelperString::translatePlural('Single', 'Plural', -1));
        self::assertSame('Single', HelperString::translatePlural('Single', 'Plural', 1));
        self::assertSame('Plural', HelperString::translatePlural('Single', 'Plural', 0));
        self::assertSame('Plural', HelperString::translatePlural('Single', 'Plural', 2));
    }
}
