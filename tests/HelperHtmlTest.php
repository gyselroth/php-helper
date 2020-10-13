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

use Gyselroth\Helper\HelperHtml;

class HelperHtmlTest extends HelperTestCase
{
    public function testDecodeHtmlSpecialChars(): void
    {
        self::assertSame('&"<>', HelperHtml::decodeHtmlSpecialChars('&amp;&quot;&lt;&gt;'));
    }

    public function testContainsEncodedHtmlSpecialChars(): void
    {
        self::assertFalse(HelperHtml::containsEncodedHtmlSpecialChars('amp;&'));
        self::assertFalse(HelperHtml::containsEncodedHtmlSpecialChars('&Gt;'));
        self::assertTrue(HelperHtml::containsEncodedHtmlSpecialChars('Stringbeforehtmlchar&quot;;'));
    }

    public function testBr2nl(): void
    {
        self::assertSame("\n\n\n\n", HelperHtml::br2nl('<br><br ><br/><br />'));
    }

    public function testUrlsToHyperlinks(): void
    {
        self::assertSame(
            '<a href="http://www.test.com/test" target="_blank">http://www.test.com/test</a> ',
            HelperHtml::urlsToHyperlinks('http://www.test.com/test')
        );

        self::assertSame(
            'This is a test link <a href="https://test.com/Test" target="_blank">https://test.com/Test</a>'
            . ' with text afterwards. See <a href="http://Further.information" target="_blank">'
            . 'http://Further.information</a> ',
            HelperHtml::urlsToHyperlinks(
                'This is a test link https://test.com/Test with text afterwards. See http://Further.information'
            )
        );
    }

    public function testStripHtmlTags(): void
    {
        self::assertSame(
            "As\n ndfghdf  gsg5eas\n\n - Aasd\n - Obflk",
            HelperHtml::stripHtmlTags(
                '<p>As<br> ndfghdf  <a href="/02gas" title="P">gsg5e</a><sup id="dfsg"'
                . ' class="hd">as</sup></p><ul><li>Aasd</li><li>Obflk</li></ul>'
            )
        );
    }

    public function testHtml2plaintext(): void
    {
        self::assertSame(
            "As\n ndfghdf gsg5eas\n - Aasd\n - Obflk",
            HelperHtml::html2plaintext('<p>As<br> ndfghdf  <a href="/02gas" title="P">gsg5e</a><sup id="dfsg"'
                . ' class="hd">as</sup></p><ul><li>Aasd</li><li>Obflk</li></ul>')
        );
    }

    public function testResizeStyles(): void
    {
        self::assertSame(
            '<div style="width:800px;height:300px;">Test</div>',
            HelperHtml::resizeStyles('<div style="width:400px;height:200px;">Test</div>', 2, 1.5)
        );

        self::assertSame(
            '<div style="width:800em;height:300em;">Test</div>',
            HelperHtml::resizeStyles('<div style="width:400em;height:200em;">Test</div>', 2, 1.5, 'em'),
            'Function not written properly (see todo in function)'
        );
    }

    public function testUmlautsToHtmlEntities(): void
    {
        self::assertSame('&auml;&ouml;&uuml;&Auml;&Ouml;&Uuml;', HelperHtml::umlautsToHtmlEntities('äöüÄÖÜ'));
    }

    public function testGetCleanedHtml(): void
    {
        self::markTestSkipped('Function different from helper function in IN2');
//        $html = file_get_contents(__DIR__ . '/Fixtures/data/files/htmlPurifierTest.html');
//        self::assertSame('', HelperHtml::getCleanedHtml($html, true, true, true, true, true));
    }

    public function testFormatArrayDump(): void
    {
        $array = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];

        $cleanDump = 'array('
            . "\n    " . '[key1] => value1'
            . "\n    " . '[key2] => value2'
            . "\n    " . '[key3] => value3'
            . "\n)\n";

        \ob_start();

        \print_r($array);

        $dump = \ob_get_clean();

        self::assertSame($cleanDump, HelperHtml::formatArrayDump($dump));
    }

    public function testRenderTableHead(): void
    {
        $th = ['row1', 'row2', 'row3'];

        self::assertSame(
            '<thead><tr><th>row1</th><th>row2</th><th>row3</th></tr></thead>',
            HelperHtml::renderTableHead($th)
        );
    }
}
