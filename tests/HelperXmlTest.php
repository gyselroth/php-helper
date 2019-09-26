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

use Gyselroth\Helper\HelperXml;

class HelperXmlTest extends HelperTestCase
{
    public function testIsValidXml(): void
    {
        $this->assertFalse(HelperXml::isValidXml('<xml/>'));
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function testGetNodes(): void
    {
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/mock.xml');
        $this->assertSame(6, count(HelperXml::getNodes($xml, [1])));
        $this->assertSame(2, count(HelperXml::getNodes($xml, [1], [], ['cdata'])));
        $this->assertSame(4, count(HelperXml::getNodes($xml, [], ['note'], ['cdata'])));
    }

    /**
     * @throws \Exception
     */
    public function testGetAmountNodesOnLevel1(): void
    {
        // document_xml-no_content.xml: Word document.xml, that has no content other than a basic document.xml structure
        /** @noinspection ReturnFalseInspection */
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/document_xml-no_content.xml');

        $this->assertEquals(2, HelperXml::getAmountNodes($xml, [1], []));
        $this->assertEquals(0, HelperXml::getAmountNodes($xml, [1], ['W:DOCUMENT']));
        $this->assertEquals(0, HelperXml::getAmountNodes($xml, [1], ['W:DOCUMENT', 'W:BODY']));
    }

    /**
     * @throws \Exception
     */
    public function testGetAmountNodesOnAllLevels(): void
    {
        // document_xml-no_content.xml: Word document.xml, that has no content other than a basic document.xml structure
        /** @noinspection ReturnFalseInspection */
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/document_xml-no_content.xml');

        $this->assertEquals(3, HelperXml::getAmountNodes($xml, []));
        $this->assertEquals(1, HelperXml::getAmountNodes($xml, [], ['W:DOCUMENT']));
        $this->assertEquals(0, HelperXml::getAmountNodes($xml, [], ['W:DOCUMENT', 'W:BODY']));
    }

    /**
     * @throws \Exception
     */
    public function testGetAmountNodesByLevel(): void
    {
        // document_xml-no_content.xml: Word document.xml, that has no content other than a basic document.xml structure
        /** @noinspection ReturnFalseInspection */
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/document_xml-no_content.xml');

        $this->assertEquals(3, HelperXml::getAmountNodes($xml, []));
        $this->assertEquals(2, HelperXml::getAmountNodes($xml, [1]));
        $this->assertEquals(1, HelperXml::getAmountNodes($xml, [2]));
    }

    /**
     * @throws \Exception
     */
    public function testGetAmountNodesByType(): void
    {
        // document_xml-no_content.xml: Word document.xml, that has no content other than a basic document.xml structure
        /** @noinspection ReturnFalseInspection */
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/document_xml-no_content.xml');

        $this->assertEquals(3, HelperXml::getAmountNodes($xml, []));
        $this->assertEquals(2, HelperXml::getAmountNodes($xml, [], [], [HelperXml::TAG_TYPE_OPEN]));
        $this->assertEquals(2, HelperXml::getAmountNodes($xml, [], [], [HelperXml::TAG_TYPE_CLOSE]));
        $this->assertEquals(2, HelperXml::getAmountNodes($xml, [], [], [HelperXml::TAG_TYPE_COMPLETE]));
        $this->assertEquals(1, HelperXml::getAmountNodes($xml, [], [], [HelperXml::TAG_TYPE_CLOSE, HelperXml::TAG_TYPE_COMPLETE]));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\XmlException
     */
    public function testValidateValid(): void
    {
        $xmlPath = __DIR__ . '/Fixtures/data/xml/mock.xml';
        $xsdPath = __DIR__ . '/Fixtures/data/xml/mock.xsd';
        $this->assertTrue(is_a(HelperXml::validate($xmlPath, $xsdPath), 'DOMDocument'));
    }

//    /**
//     * @throws \Gyselroth\Helper\Exception\XmlException
//     */
//    public function testValidateVersion()
//    {
//        $xmlPath = __DIR__ . '/Fixtures/data/xml/mock.xml';
//        $xsdPath = __DIR__ . '/Fixtures/data/xml/mock.xsd';
//        $this->assertTrue(HelperXml::validate($xmlPath, $xsdPath, '0.9')->version == '0.9');
//    }

    /**
     * @throws \Gyselroth\Helper\Exception\XmlException
     */
    public function testValidateInvalid(): void
    {
        $xmlPath = __DIR__ . '/Fixtures/data/xml/mock.xml';
        $xsdPath = __DIR__ . '/Fixtures/data/xml/invalid_mock.xsd';
        $this->assertFalse(HelperXml::validate($xmlPath, $xsdPath));
    }

    /**
     * @expectedException        \Gyselroth\Helper\Exception\XmlException
     * @expectedExceptionMessage XSD
     */
    public function testValidateMissingXsd(): void
    {
        $xmlPath = __DIR__ . '/Fixtures/data/xml/mock.xml';
        $xsdPath = __DIR__ . '/invalid/path/to/mock.xsd';
        HelperXml::validate($xmlPath, $xsdPath);
    }

    /**
     * @expectedException        \Gyselroth\Helper\Exception\XmlException
     * @expectedExceptionMessage XML
     */
    public function testValidateMissingXml(): void
    {
        $xmlPath = __DIR__ . '/invalid/path/to/mock.xml';
        $xsdPath = __DIR__ . '/Fixtures/data/xml/mock.xsd';
        HelperXml::validate($xmlPath, $xsdPath);
    }

    public function testDebugPrint(): void
    {
        $xmlPath = __DIR__ . '/Fixtures/data/xml/mock.xml';
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->load($xmlPath);
        ob_start();
        HelperXml::debugPrint($xml);
        $debugPrint = ob_end_clean();
        $this->assertTrue($debugPrint == file_get_contents($xmlPath));
    }

    public function testStrReplaceNodeValues(): void
    {
        $xmlPath = __DIR__ . '/Fixtures/data/xml/mock.xml';
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->load($xmlPath);
        $xmlPath2 = __DIR__ . '/Fixtures/data/xml/mock2.xml';
        $xml2 = new \DOMDocument('1.0', 'UTF-8');
        $xml2->load($xmlPath2);
        $this->assertEquals($xml2, HelperXml::strReplaceNodeValues(['Gyselroth','Ewald','Reminder','I am'],['Philippe','Kay','Confirmation','He is'], $xml));
    }

    /**
     * @throws \Exception
     */
    public function testGetTagsFromXml(): void
    {
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/mock.xml');
        $tags = HelperXml::getTagsFromXml($xml);
        $this->assertSame('NOTE',                       $tags[0]['tag']);
        $this->assertSame('open',                       $tags[0]['type']);
        $this->assertSame('complete',                   $tags[1]['type']);
        $this->assertSame('cdata',                      $tags[2]['type']);
        $this->assertSame('I am currently testing.',    $tags[7]['value']);
    }

    public function testFormatXmlString(): void
    {
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/mock3.xml');
        $xml2 = file_get_contents(__DIR__ . '/Fixtures/data/xml/mock.xml');
        $this->assertEquals($xml2, HelperXml::formatXmlString($xml));
    }

    public function testXmlNodeToArray(): void
    {
        $xml = file_get_contents(__DIR__ . '/Fixtures/data/xml/mock.xml');
        $xmlArray = HelperXml::xmlNodeToArray(simplexml_load_string($xml));
        $this->assertSame('Gyselroth',                  $xmlArray['to']);
        $this->assertSame('I am currently testing.',    $xmlArray['body']);
    }
}
