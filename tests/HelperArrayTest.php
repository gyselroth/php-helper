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

use Gyselroth\Helper\HelperArray;
use PHPUnit\Framework\Constraint\IsType;

class HelperArrayTest extends HelperTestCase
{
    public $data = [
        'Brief_Anrede'             => 'Sehr geehrter Herr Foo, sehr geehrte Frau Bar-Baz',
        'Brief_Bezeichner'         => '5112398 1003 Joeline Doe',
        'Brief_Datum'              => ' 7. February 2018',
        'Brief_Ort'                => 'Lorem-Ipsum am See',
        'Brief_Schultyp'           => 'Foo-Gymnasium',
        'Brief_Verfasser'          => 'Dr. Foo Bar, Prorektor',
        'Eltern_Anrede'            => 'The lama visualizes.',
        'Eltern_Name'              => 'Spiritual uniquenesses hurts.',
        'Eltern_Ort'               => '1234 Foo am See',
        'Eltern_Strasse'           => 'Zion  64',
        'Image_Brief_Unterschrift' => '../tests/Fixtures/data/images/signatures/signature1.png',
        'Note_Durchschnitt'        => 4.84,
        'Pronomen_Dativ_lc'        => 'ihr',
        'Pronomen_Nominativ_uc'    => 'sie',
        'Schueler_Vorname'         => 'Joeline',
        'Schule_Email'             => 'info@foogymnasium.ch',
        'Schule_Name'              => 'Kantonsschule Foo am See',
        'Schule_Ort'               => '1234 Foo am See',
        'Schule_Strasse'           => 'Foostrasse 123',
        'Schule_Telefon'           => '012 345 67 89',
        'Schule_Webseite'          => '',
        'Block1'                   => [
            0 => [
                'Note_Fach'      => 'Deutsch',
                'Note_Note'      => 4.88,
                'Note_Bemerkung' => '(Aufsatz: 5.75, Sprachprüfung: 4)',
            ],
            1 => [
                'Note_Fach'      => 'Mathematik',
                'Note_Note'      => 3.5,
                'Note_Bemerkung' => ''
            ]
        ],
        'Note_Erfahrungsnoten'     => 'unter Einbezug der Erfahrungsnoten (Deutsch: 5.5, Mathematik: 5.5)',
        'Erfahrungsnote_Deutsch'   => 5.5,
        'Erfahrungsnote_Mathe'     => 5.5
    ];

    public function testIsAssociativeWhenNotAssotiative(): void
    {
        $this->assertFalse(HelperArray::isAssociative([
            0 => [
                'test' => 'test'
            ],
            1 => [
                'test' => 'test'
            ]
        ]));
    }

    public function testIsAssociative(): void
    {
        $this->assertTrue(HelperArray::isAssociative($this->data));
    }

    public function testIsAssociativeInParts(): void
    {
        $dataStructure = [
            0             => $this->data,
            1             => $this->data,
            'opt_block_a' => true,
            'opt_block_b' => true
        ];

        $this->assertTrue(HelperArray::isAssociative($dataStructure));
    }

    public function testIntVal(): void
    {
        $this->assertSame(array_values([10, 1, -2, 0, 2, 3]),
            array_values(HelperArray::intVal(['10', 1, -2, 0, 1, 2, 3, 1], true)));

        // Test: convert mixed (int and numeric string) array to array of int
        $result = HelperArray::intVal([1, '2', 3]);
        $this->assertThat(
            $result,
            new IsType('array')
        );

        $this->assertNotEmpty($result);
        $this->assertCount(3, $result);
        $this->assertEquals(2, $result[1]);
        $this->assertEquals(6, array_sum($result));

        // Test option: array-unique
        $result = HelperArray::intVal([1, '2', 2, 3, 2], true);
        $this->assertThat(
            $result,
            new IsType('array')
        );
        $this->assertNotEmpty($result);
        $this->assertCount(3, $result);
        $this->assertEquals(6, array_sum($result));

        // Test options: 1) array-unique and 2) convert non-numerical to 0
        $result = HelperArray::intVal([1, 'x', '2', 2, 3, 2], true, true);
        $this->assertThat(
            $result,
            new IsType('array')
            );
        $this->assertNotEmpty($result);
        $this->assertCount(4, $result);
        $this->assertEquals(6, array_sum($result));
    }

    public function testIntValSubItemsByKey(): void
    {
        $array = [
            '1' => ['test1' => '1', 'test2' => '2', 'test3' => '3'],
            '2' => ['test1' => '1', 'test2' => '2', 'test3' => '3'],
            '3' => ['test1' => '1', 'test2' => '2', 'test3' => '3']
        ];
        $keys  = ['test2', 'test3'];
        $this->assertThat(
            HelperArray::castSubColumn($array, $keys)['1']['test2'],
            new IsType('int')
        );
        $this->assertThat(
            HelperArray::castSubColumn($array, $keys)['1']['test3'],
            new IsType('int')
        );
        $this->assertThat(
            HelperArray::castSubColumn($array, $keys)['2']['test2'],
            new IsType('int')
        );
        $this->assertThat(
            HelperArray::castSubColumn($array, $keys)['2']['test3'],
            new IsType('int')
        );
        $this->assertThat(
            HelperArray::castSubColumn($array, $keys)['3']['test2'],
            new IsType('int')
        );
        $this->assertThat(
            HelperArray::castSubColumn($array, $keys)['3']['test3'],
            new IsType('int')
        );
    }

    public function testIntExplode(): void
    {
        $this->markTestSkipped('@see Application_Helper_Numeric::intExplode()');
    }

    public function testIntImplode(): void
    {
        $this->assertSame('10,1,-2,0,2,3',
            HelperArray::intImplode(['10', 1, -2, 0, 1, 2, 3, 1]));
    }

//    public function testTrim()
//    {
//        $this->assertSame(HelperArray::trim(['10 ', '', ' df '], true), ['10', '', 'df']);
//        $this->assertSame(HelperArray::trim([10, '', ' d f ']), [10, 'd f'], 'PHP trim function makes string out of int.');
//    }

    public function testGetItemByKeyValue(): void
    {

        $array = [
            '1'  => [],
            '1a' => ['a' => 'ameise', 'b' => 'bär', 'c' => 'chameleon', 'd' => 'delfin', 'e' => 'elefant'],
            2    => ['a' => 1, 'b' => 2],
            'x'  => ['a' => 'ant', 'b' => 'bear', 'c' => 'chameleon', 'd' => 'dolphin', 'e' => 'elephant']
        ];
        $this->assertSame(
            ['a' => 'ant', 'b' => 'bear', 'c' => 'chameleon', 'd' => 'dolphin', 'e' => 'elephant'],
            HelperArray::getItemByKeyValue($array, 'e', 'elephant'))
        ;

        $array = [
            '1'  => [],
            '1a' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'x'  => ['a' => 11, 'b' => '2', 'c' => 33, 'd' => 44, 'e' => 55],
            2    => ['a' => 1, 'b' => 2],
        ];
        $this->assertSame(
            ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            HelperArray::getItemByKeyValue($array, 'b', '2', false)
        );
        $this->assertSame(
            ['a' => 11, 'b' => '2', 'c' => 33, 'd' => 44, 'e' => 55],
            HelperArray::getItemByKeyValue($array, 'b', '2', true)
        );

        $this->assertNull(HelperArray::getItemByKeyValue($array, 'z', 'z'));
    }

    public function testSearchValueInMultidimensionalArray(): void
    {
        $array = [
            '1'  => [],
            '1a' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
            'x'  => ['a' => 11, 'b' => '2', 'c' => 33, 'd' => 44, 'e' => 55],
            2    => ['a' => 1, 'b' => 2],
        ];
        $this->assertSame('x', HelperArray::searchValueInMultidimensionalArray('2', 'b', $array));

        $this->assertNull(HelperArray::searchValueInMultidimensionalArray('z', 'z', $array));
    }

    public function testGetValueByKeyFromSubArrays(): void
    {
        $array = [
            '1' => [
                '11' => ['111' => ['1111' => 'value1111'], '112' => ['1121' => 'value1121']],
                '12' => ['121' => ['1211' => 'value1211'], '122' => ['1221' => 'value1221']]
            ],
            '2' => [
                '21' => ['211' => ['2111' => 'value2111'], '212' => ['2121' => 'value2121']],
                '22' => ['221' => ['2211' => 'value2211'], '222' => ['2221' => 'value2221']]
            ]
        ];
        $this->assertSame('value1221', HelperArray::getValueByKeyFromSubArrays($array, '1', '12', '122', '1221'));
        $this->assertSame('{"1221":"value1221"}', json_encode(HelperArray::getValueByKeyFromSubArrays($array, '1', '12', '122')));
        $this->assertFalse(HelperArray::getValueByKeyFromSubArrays($array, '1', '12', '122', '1220'));
        $this->assertTrue(HelperArray::getValueByKeyFromSubArrays($array, '1', '12', '122', '1220', true));
    }

    public function testKeysFromIDs(): void
    {
        $array = [
            '0' => [
                'id'  => "200",
                'id2' => "700"
            ],
            '1' => [
                'id'  => "100",
                'id2' => "1000"
            ],
            '2' => [
                'id'  => "300",
                'id2' => "500"
            ]
        ];
        $this->assertSame('{"200":{"id":"200","id2":"700"},"100":{"id":"100","id2":"1000"},"300":{"id":"300","id2":"500"}}',
            json_encode(HelperArray::keysFromIDs($array)));
        $this->assertSame('{"500":{"id":"300","id2":"500"},"700":{"id":"200","id2":"700"},"1000":{"id":"100","id2":"1000"}}',
            json_encode(HelperArray::keysFromIDs($array, 'id2', true)));
    }

    public function testKeysExist(): void
    {
        $array = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];
        $this->assertTrue(HelperArray::keysExist($array, ['key1', 'key2', 'key3']));
        $this->assertTrue(HelperArray::keysExist($array, ['key1', 'key3']));
        $this->assertFalse(HelperArray::keysExist($array, ['key1', 'key', 'key3']));
    }

    public function testReplaceInKeys(): void
    {
        $array = [
            'id' => [
                'id'  => ['id' => ['id' => 'value1111'], 'id2' => ['id' => 'value1121']],
                'id3' => ['id' => ['id' => 'value1211'], 'id4' => ['id' => 'value1221']]
            ]
        ];
        HelperArray::replaceInKeys('id', 'newid', $array);
        $this->assertSame(
            '{"newid":{"newid":{"newid":{"newid":"value1111"},"newid2":{"newid":"value1121"}},"newid3":{"newid":{"newid":"value1211"},"newid4":{"newid":"value1221"}}}}',
            json_encode($array)
        );
    }

    public function testInArrayRecursive(): void
    {
        $array = [
            'id' => [
                'id'  => ['id' => ['id' => '1111'], 'ID2' => ['id' => '1121']],
                'ID2' => ['id' => ['id' => '1211'], 'ID2' => ['id' => '1221']]
            ]
        ];
        $this->assertTrue(HelperArray::inArrayRecursive(1121, $array));
        $this->assertFalse(HelperArray::inArrayRecursive(1121, $array, true));
    }

    public function testIsMultiDimensional(): void
    {
        $this->assertTrue(HelperArray::isMultiDimensional(['t' => ['e' => 's'], ['t']]));
        $this->assertFalse(HelperArray::isMultiDimensional(['t' => 'e']));
    }

    public function testFlatten(): void
    {
        $array = [
            '1' => [
                'id'  => '200',
                'id2' => 'value1'
            ],
            '2' => [
                'id'  => '100',
                'id2' => 'value2'
            ],
            '3' => [
                'id'  => '300',
                'id2' => 'value3'
            ]
        ];

        $this->assertSame(json_encode(array_column($array, 'id')), '["200","100","300"]');
    }

    public function testGetArrayFromRelatedIdsList(): void
    {
        $this->assertSame('[1234,2,293,33]', json_encode(HelperArray::getArrayFromRelatedIdsList(['id_1234', 'lp_2L30', 'id_293', 'po_33'])));
        $this->assertSame('[1234,2,293,33]', json_encode(HelperArray::getArrayFromRelatedIdsList('id_1234,lp_2L30,id_293,po_33')));
        $this->assertSame('[1234,293]', json_encode(HelperArray::getArrayFromRelatedIdsList('id.1234,lp.230,id.293,po.33,id.1234', 'id', '.')));
    }

    public function testRemoveItemsByValue(): void
    {
        $array = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];
        $this->assertSame('{"key1":"value1","key3":"value3"}',
            json_encode(HelperArray::removeItemsByValue($array, ['value2'])));
    }

    public function testRemoveItemsByValues(): void
    {
        $array = [
            'key1' => [
                'subkey1' => '1',
                'subkey2' => '2',
                'subkey3' => '3'
            ],
            'key2' => [
                'subkey1' => '3',
                'subkey2' => '2',
                'subkey3' => '1'
            ],
            'key3' => [
                'subkey1' => '2',
                'subkey2' => '1',
                'subkey3' => '3'
            ]
        ];
        $this->assertSame('{"key2":{"subkey1":"3","subkey2":"2","subkey3":"1"}}',
            json_encode(HelperArray::removeItemsByValues($array, ['1', '2'], 'subkey1')));
    }

    public function testConvertDataTypesOfQueryResult(): void
    {
        $array     = [
            '0' => [
                'roomId'                => '15',
                'subjectId'             => 'substitution',
                'subjectShortNameUntis' => 'holiday,test1,test2',
                'subjectMappingId'      => '1,2,3,4'
            ],
            '1' => [
                'roomId'                => '16',
                'subjectId'             => 'substitution',
                'subjectShortNameUntis' => 'holiday,test3,test4',
                'subjectMappingId'      => '5,6,7,8'
            ]
        ];
        $dataTypes = [
            'roomId'                => 'int',
            'subjectShortNameUntis' => 'array.string',
            'subjectMappingId'      => 'array.int'
        ];
        $this->assertSame(
            '[{"roomId":15,"subjectId":"substitution","subjectShortNameUntis":["holiday","test1","test2"],"subjectMappingId":[1,2,3,4]},{"roomId":16,"subjectId":"substitution","subjectShortNameUntis":["holiday","test3","test4"],"subjectMappingId":[5,6,7,8]}]',
            json_encode(HelperArray::convertDataTypesOfQueryResult($array, $dataTypes))
        );
    }

    // @todo: Kay: can not be tested yet, because the convertArrayDataByTypes() function should be changed to the suggested solution or similar.
    /*
    public function testConvertArrayDataByTypes()
    {
        $array = [
            '0' => [
                'value' => '15',
                'type'  => 'INT'
            ],
            '1' => [
                'value' => 'test',
                'type'  => 'string'
            ],
            '2' => [
                'values' => ['value' => ['value1', 'value2', 'value3']],
                'type'   => 'arrAy'
            ],
            '3' => [
                'values' => ['value' => ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']],
                'type'   => 'object'
            ],
            '4' => [
                'value' => '2',
                'type'  => 'notype'
            ],
            '5' => [
                'values' => ['value' => ['1', 2]],
                'type'  => 'notype'
            ]
        ];
        $this->assertSame(json_encode(HelperArray::convertArrayDataByTypes($array)),
            '[15,"test",["value1","value2","value3"],{"key1":"value1","key2":"value2","key3":"value3"},"2",["1",2]]');
    }
    */

    public function testGetUniqueKey(): void
    {
        $array = [
            '2'   => 2,
            '2_1' => 3,
            '3'   => 5
        ];
        $this->assertSame('2_1_1', HelperArray::getUniqueKey('2', $array));
    }

    public function testArrayUniqueByKey(): void
    {
        $array = [
            '0' => [
                'value' => '1',
                'type'  => '3'
            ],
            '1' => [
                'value' => '2',
                'type'  => '3'
            ],
            '2' => [
                'value' => '1',
                'type'  => '0'
            ]
        ];
        $this->assertSame('[{"value":"1","type":"3"},{"value":"2","type":"3"}]', json_encode(HelperArray::arrayUniqueByKey($array, 'value')));
        $this->assertSame('[{"value":"1","type":"3"},{"value":"1","type":"0"}]', json_encode(HelperArray::arrayUniqueByKey($array, 'type')));
    }

    public function testArrayMultidimensionalSortByKey(): void
    {
        $array = [
            '0' => [
                'value' => '1',
                'type'  => '3'
            ],
            '1' => [
                'value' => '2',
                'type'  => '2'
            ],
            '2' => [
                'value' => '3',
                'type'  => '1'
            ]
        ];
        HelperArray::arrayMultidimensionalSortByKey($array, 'type');
        $this->assertSame('[{"value":"3","type":"1"},{"value":"2","type":"2"},{"value":"1","type":"3"}]', json_encode($array));
        HelperArray::arrayMultidimensionalSortByKey($array, 'value', SORT_DESC);
        $this->assertSame('[{"value":"3","type":"1"},{"value":"2","type":"2"},{"value":"1","type":"3"}]', json_encode($array));
    }

    // @todo: Daniel: can not be tested because the purpose of arrayMultidimensionalSortByKeyAndCheck() is unclear
    /*
    public function testArrayMultidimensionalSortByKeyAndCheck()
    {
        $array = [
            '0' => [
                'value' => ['._-"', '*------*'],
                'type'  => [2, 5, 1]
            ],
            '1' => [
                'value' => [2, 5, 1],
                'type'  => ['int', 0, 4],
            ],
            '2' => [
                'value' => ['int', 0, 4],
                'type'  => ['._-"', '*------*']
            ]
        ];
        $this->assertSame(json_encode(HelperArray::arrayMultidimensionalSortByKeyAndCheck($array, 'value',
            'int', SORT_DESC)),
            '[{"value":["int",0,4],"type":["._-\"","*------*"]},{"value":[2,5,1],"type":["int",0,4]},{"value":["._-\"","*------*"],"type":[2,5,1]}]');
        $this->assertSame(json_encode(HelperArray::arrayMultidimensionalSortByKeyAndCheck($array, 'type',
            0)),
            '[{"value":["int",0,4],"type":["._-\"","*------*"]},{"value":[2,5,1],"type":["int",0,4]},{"value":["._-\"","*------*"],"type":[2,5,1]}]');
    }
    */

    public function testKeys_recursive(): void
    {
        $array = [
            'a' => ['a' => 1, 'b' => 2, 'c' => 3],
            'b' => ['a' => 1, 'b' => 2, 'c' => 3],
            'c' => ['d' => 1, 'e' => 2, 'f' => 3]
        ];
        $this->assertSame('a,b,c,d,e,f', implode(',', HelperArray::keys_recursive($array)));
    }

    public function testAddKeysToSubArray(): void
    {
        $array = [
            'key1' => ['index1' => 'value1', 'index2' => 'value2'],
            'key2' => ['index3' => 'value3', 'index4' => 'value4'],
            'key3' => ['index5' => 'value5', 'index6' => 'value6']
        ];
        $this->assertSame('{"index1":{"key1":"value1"},"index2":{"key1":"value2"},"index3":{"key2":"value3"},"index4":{"key2":"value4"}}',
            json_encode(HelperArray::addKeysToSubArray($array, ['key1', 'key2'])));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testStrSplManipulateStrToUpper(): void
    {
        $this->assertSame(['AETHERES', 'CAELOS', 'DEMOLITIONE', 'EXSUL', 'HAMBURGUM', 'NAVIS', 'NOMEN', 'VENTUS'],
            HelperArray::strSplManipulate(['aetheres', 'caelos', 'demolitione', 'exsul', 'hamburgum', 'navis', 'nomen', 'ventus'], 'strtoupper'));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testStrSplManipulateStrToLower(): void
    {
        $this->assertSame(['aetheres', 'caelos', 'demolitione', 'exsul', 'hamburgum', 'navis', 'nomen', 'ventus'],
            HelperArray::strSplManipulate(['AETHERES', 'CAELOS', 'DEMOLITIONE', 'EXSUL', 'HAMBURGUM', 'NAVIS', 'NOMEN', 'VENTUS'], 'strtolower'));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testStrSplManipulateUcFirst(): void
    {
        $this->assertSame(['Aetheres', 'Caelos', 'Demolitione', 'Exsul', 'Hamburgum', 'Navis', 'Nomen', 'Ventus'],
            HelperArray::strSplManipulate(['aetheres', 'caelos', 'demolitione', 'exsul', 'hamburgum', 'navis', 'nomen', 'ventus'], 'ucfirst'));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testStrSplManipulateStrRev(): void
    {
        $this->assertSame(['serehtea', 'soleac', 'enoitilomed', 'lusxe', 'mugrubmah', 'sivan', 'nemon', 'sutnev'],
            HelperArray::strSplManipulate(['aetheres', 'caelos', 'demolitione', 'exsul', 'hamburgum', 'navis', 'nomen', 'ventus'], 'strrev'));
    }

    // @todo: Cannot be tested because $value in getValuesByKeys() is not an array
    /*
    public function testgetValuesByKeys()
    {
        $array = [
            'key1' => 'value1',
            'key2' => ['subkey1' => 'value2.1', 'subkey2' => 2],
            'key3' => 'value3',
            'key4' => 'value4',
            'key5' => 'value5'
        ];
        $this->assertSame(json_encode(HelperArray::getValuesByKeys(['key1', 'key2', 'key5'], $array,
            ['subkey2' => '2'], false)), '{"key1":"value1","key5":"value5"}');
        $this->assertSame(json_encode(HelperArray::getValuesByKeys(['key1', 'key2', 'key5'], $array,
            ['subkey2' => '2'], true)), '{"key1":"value1","key2":{"subkey1":"value2.1","subkey2":2},"key5":"value5"}');
    }
    */

    public function testMergeArraysByArrayIndexID(): void
    {
        $this->assertFalse(HelperArray::mergeArraysByArrayIndexID(false));
    }

//    public function testChangeKeyName()
//    {
//        $array_old = [
//            'test' => 2,
//            '2'    => ['test' => 1, '2' => ['1' => true, 'test' => 6]],
//            3      => 5
//        ];
//        $array_new = [
//            'TEST' => 2,
//            '2'    => ['TEST' => 1, '2' => ['1' => true, 'TEST' => 6]],
//            3      => 5
//        ];
//        $this->assertSame(HelperArray::changeKeyName($array_old,'TEST', 'test'), $array_new, 'Order of array changed');
//    }

    public function testGetCsvFromArray(): void
    {
        $array = ['1', '2', ['3', '4']];
        $this->assertSame('1,2,3,4', HelperArray::getCsvFromArray($array));
    }

    public function testGetArrayFromCsvInRows(): void
    {
        $csv = "1,2,3,4\r\r5,6,7,8\n9,10 test,test,12\r13,14,15,test 2";
        $this->assertSame('[["1","2","3","4"],["5","6","7","8"],["9","10 test","test","12"],["13","14","15","test 2"]]',
            json_encode(HelperArray::getArrayFromCsvInRows($csv)));
    }

    public function testSubstrKeys(): void
    {
        $array = [
            'key1' => 'value1',
            'key2' => ['subkey1' => 'value2'],
            'key3' => 'value3',
            'key4' => 'value4',
            'key5' => 'value5'
        ];
        $this->assertSame('{"y1":"value1","y2":{"subkey1":"value2"},"y3":"value3","y4":"value4","y5":"value5"}',
            json_encode(HelperArray::substrKeys($array, 2, 2)));
        $this->assertSame('{"1":"value1","2":{"subkey1":"value2"},"3":"value3","4":"value4","5":"value5"}',
            json_encode(HelperArray::substrKeys($array, 3, false)));
    }

    public function testStrReplaceAssociative(): void
    {
        $array = [
            '. '      => ', ',
            'Der'     => 'wobei der',
            'sollte ' => '',
            'n.'      => 'n sollte.'
        ];
        $this->assertSame('Dies ist ein Test, wobei der String stückweise ersetzt werden sollte.',
            HelperArray::strReplaceAssociative('Dies ist ein Test. Der String sollte stückweise ersetzt werden.', $array));
    }

    public function testImplodeWrapped(): void
    {
        $array = [1, 2, '3', '4'];
        $this->assertSame("'1','2','3','4'", HelperArray::implodeWrapped($array));
        $this->assertSame('<1>.<2>.<3>.<4>', HelperArray::implodeWrapped($array, '<', '>', '.'));
    }

    public function testSortByKey(): void
    {
        $array1 = [
            'time' => 1,
            'key2' => 2,
            'key3' => 3,
            'key4' => 5
        ];
        $array2 = [
            'time' => 1,
            'key2' => '2',
            'key3' => 4,
            'key4' => 4
        ];
        $this->assertSame(0, HelperArray::sortByKey($array1, $array2));
        $this->assertSame(1, HelperArray::sortByKey($array1, $array2, $key = 'key2', true));
        $this->assertSame(1, HelperArray::sortByKey($array1, $array2, $key = 'key3'));
        $this->assertSame(-1, HelperArray::sortByKey($array1, $array2, $key = 'key4'));
    }

    public function testPrepareDataForXLSFormat(): void
    {
        $this->markTestSkipped('@see Application_Helper_Excel::prepareDataForXLSFormat()');
    }

    public function testArrayStrPpos(): void
    {
        $needles1 = ['string ', 'deR', 'zum  '];
        $needles2 = ['teststring'];
        $haystack = 'Teststring zum Überprüfen der Funktion.';
        $this->assertTrue(HelperArray::arrayStrPos($needles1, $haystack));
        $this->assertFalse(HelperArray::arrayStrPos($needles2, $haystack));
    }

    public function testReIndexByKey(): void
    {
        $rows = [
            ['key1' => 1, 'key2' => 2],
            ['key1' => 3, 'key2' => 4],
            ['key1' => 5, 'key2' => 'test'],
            ['key1' => 7, 'key2' => 8]
        ];
        $this->assertSame('{"2":{"key1":1,"key2":2},"4":{"key1":3,"key2":4},"test":{"key1":5,"key2":"test"},"8":{"key1":7,"key2":8}}',
            json_encode(HelperArray::reIndexByKey($rows, 'key2')));
    }

    public function testSet(): void
    {
        $array = [
            'key1' => 1,
            'key2' => 2,
            'key3' => [
                'key3_1' => 3,
                'key3_2' => [
                    'key3_2_1' => 4
                ]
            ],
            'key4' => 5
        ];
        HelperArray::set($array, 'key3.key3_2.key3_2_1', '8');
        $this->assertSame('{"key1":1,"key2":2,"key3":{"key3_1":3,"key3_2":{"key3_2_1":"8"}},"key4":5}', json_encode($array));
        HelperArray::set($array, 'key3.key3_2.key3_2_1.5', '8');
        $this->assertSame('{"key1":1,"key2":2,"key3":{"key3_1":3,"key3_2":{"key3_2_1":{"5":"8"}}},"key4":5}', json_encode($array));
    }

    public function testGet(): void
    {
        include_once __DIR__ . '/Fixtures/data/interfaces/ArrayAccess.php';
        $object = new Object_ArrayAccess();

        $array = [
            'key1' => 1,
            'key2' => 2,
            'key3' => [
                'key3_1' => 3,
                'key3_2' => [
                    'key3_2_1' => 4
                ]
            ],
            'key4' => 5
        ];
        $this->assertSame(4, HelperArray::get($array, 'key3.key3_2.key3_2_1'));
        $this->assertSame(4, HelperArray::get($object, 'key3.key3_2.key3_2_1'));
        $this->assertSame('not found', HelperArray::get($array, 'key3.key3_3', 'not found'));
    }

    public function testIsAccessible(): void
    {
        include_once __DIR__ . '/Fixtures/data/interfaces/ArrayAccess.php';
        $object = new Object_ArrayAccess();
        $array  = [
            'key1' => 1,
            'key2' => 2,
            'key3' => 3,
            'key4' => 4
        ];
        $this->assertTrue(HelperArray::isAccessible($array));
        $this->assertTrue(HelperArray::isAccessible($object));
    }

    public function testExists(): void
    {
        include_once __DIR__ . '/Fixtures/data/interfaces/ArrayAccess.php';
        $object = new Object_ArrayAccess();
        $array  = [
            'key1' => 1,
            'key2' => 2,
            'key3' => 3,
            'key4' => 4
        ];
        $this->assertTrue(HelperArray::keyExists($array, 'key2'));
        $this->assertTrue(HelperArray::keyExists($object, 'key2'));
        $this->assertFalse(HelperArray::keyExists($array, 'key5'));
    }

    public function testHasStringKeys(): void
    {
        $this->assertTrue(HelperArray::hasStringKeys(['a' => 0, 'b' => 1]));
//        $this->assertTrue(HelperArray::hasStringKeys(['0' => 'a', '1' => 'b']), 'Should be true since \'0\' and \'1\' are strings');

        $this->assertFalse(HelperArray::hasStringKeys([]));
        $this->assertFalse(HelperArray::hasStringKeys([0, 1, 2]));

        $this->assertFalse(HelperArray::hasStringKeys([0 => [1 => 'b']]));

        $this->assertFalse(HelperArray::hasStringKeys([0 => ['a' => 'b']]));
    }

    public function testExtendArray(): void
    {
        $this->assertSame([], HelperArray::extendArray([], []));

        $this->assertSame([[1, 4], [2, 5], [3, 6]], HelperArray::extendArray([1, 2, 3], [4, 5, 6]));

        $array1 = ['ids' => 98, 'titles' => 'Mr', 'names' => 'Ed'];
        $array2 = ['ids' => 99, 'titles' => 'Ms', 'names' => 'Addison'];
        $this->assertSame($array2, HelperArray::extendArray([], $array2));

        $this->assertSame(
            ['ids' => [98, 99], 'titles' => ['Mr', 'Ms'], 'names' => ['Ed', 'Addison']],
            HelperArray::extendArray($array1, $array2)
        );

        $array3 = ['offsets' => 97, 'countries' => 'Switzerland'];
        $this->assertSame(
            ['ids' => 98, 'titles' => 'Mr', 'names' => 'Ed', 'offsets' => 97, 'countries' => 'Switzerland'],
            HelperArray::extendArray($array1, $array3)
        );

        $array4 = ['ids' => 99, 'offsets' => 97, 'countries' => 'Switzerland'];
        $this->assertSame(
            ['ids' => [98, 99], 'titles' => 'Mr', 'names' => 'Ed', 'offsets' => 97, 'countries' => 'Switzerland'],
            HelperArray::extendArray($array1, $array4)
        );
    }

    public function testCastSubColumn(): void
    {
        $array = [
            '1' => ['test1' => '1', 'test2' => '2', 'test3' => false],
            '2' => ['test1' => '1', 'test2' => 2, 'test3' => '3'],
            '3' => ['test1' => '1', 'test2' => true, 'test3' => 3.2]
        ];
        $keys  = ['test2', 'test3'];

        $this->assertNotEmpty(HelperArray::castSubColumn($array, $keys, ''));
        $this->assertNotEmpty(HelperArray::castSubColumn($array, $keys, 'null'));
        $this->assertNotEmpty(HelperArray::castSubColumn($array, $keys, 'array'));
        $this->assertNotEmpty(HelperArray::castSubColumn($array, $keys, 'object'));
        $this->assertNotEmpty(HelperArray::castSubColumn($array, $keys, '123abc'));

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys)['1']['test2'],
            new IsType('int')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys)['1']['test3'],
            new IsType('int')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys)['2']['test2'],
            new IsType('int')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys)['2']['test3'],
            new IsType('int')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys)['3']['test2'],
            new IsType('int')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys)['3']['test3'],
            new IsType('int')
        );


        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'bool')['1']['test2'],
            new IsType('bool')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'bool')['1']['test3'],
            new IsType('bool')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'bool')['2']['test2'],
            new IsType('bool')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'bool')['2']['test3'],
            new IsType('bool')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'bool')['3']['test2'],
            new IsType('bool')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'bool')['3']['test3'],
            new IsType('bool')
        );


        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'float')['1']['test2'],
            new IsType('float')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'float')['1']['test3'],
            new IsType('float')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'float')['2']['test2'],
            new IsType('float')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'float')['2']['test3'],
            new IsType('float')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'float')['3']['test2'],
            new IsType('float')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'float')['3']['test3'],
            new IsType('float')
        );


        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'string')['1']['test2'],
            new IsType('string')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'string')['1']['test3'],
            new IsType('string')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'string')['2']['test2'],
            new IsType('string')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'string')['2']['test3'],
            new IsType('string')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'string')['3']['test2'],
            new IsType('string')
        );

        $this->assertThat(
            HelperArray::castSubColumn($array, $keys, 'string')['3']['test3'],
            new IsType('string')
        );


    }
}
