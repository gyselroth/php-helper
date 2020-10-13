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
use Gyselroth\HelperLog\Exception\LoggerException;
use Gyselroth\Helper\Exception\ZipException;
use Gyselroth\Helper\HelperFile;
use Gyselroth\Helper\HelperZip;

class HelperZipTest extends HelperTestCase
{
    /**
     * @throws Exception
     * @throws LoggerException
     * @throws ZipException
     */
    public function testZipFiles(): void
    {
        self::markTestIncomplete('@todo: Review and correct test and rel. method');

//        $path  = __DIR__ . '/Fixtures/data/files/zip';
//
//        /** @noinspection ReturnFalseInspection */
//        $files = \scandir($path, null);
//
//        unset($files[0], $files[1]);
//
//        $files = \array_values($files);
//
//        $pathTmp        = HelperFile::getGlobalTmpPath();
//        $pathTmpExisted = \is_dir($pathTmp);
//
//        if (!$pathTmpExisted) {
//            $pathTmpExisted = false;
//
//            \mkdir($pathTmp);
//        }
//
//        $pathDestinationFile = $pathTmp . DIRECTORY_SEPARATOR . 'tmp.zip';
//
//        if (\file_exists($pathDestinationFile)) {
//            \unlink($pathDestinationFile);
//        }
//
//        HelperZip::zipFiles($files, $pathDestinationFile, false, $path, false);
//
//        self::assertFileExists($pathDestinationFile);
//        self::assertGreaterThan(0, \filesize($pathDestinationFile));
//
//        $pathUnzip = $pathTmp . DIRECTORY_SEPARATOR . 'unzip';
//
//        HelperZip::unzip($pathDestinationFile, $pathUnzip);
//
//        self::assertFileEquals($path, $pathUnzip);
//
//        if (\file_exists($pathDestinationFile)) {
//            // Clean up
//            \unlink($pathDestinationFile);
//        }
//
//        if (!$pathTmpExisted) {
//            HelperFile::rmdirRecursive($pathTmp);
//        }
//
//        if (is_dir($pathUnzip)) {
//            HelperFile::rmdirRecursive($pathUnzip);
//        }
    }

    /**
     * @throws Exception
     * @throws LoggerException
     */
    public function testZip(): void
    {
        $path = __DIR__ . '/Fixtures/data/files/zip/01.pdf';

        $pathTmp = HelperFile::getGlobalTmpPath(true);
        $pathDestinationFile = $pathTmp . DIRECTORY_SEPARATOR . 'test.zip';

        HelperZip::zip($path, $pathDestinationFile, false);

        self::assertFileExists($pathDestinationFile);
        self::assertGreaterThan(0, filesize($pathDestinationFile));

        if (file_exists($pathDestinationFile)) {
            unlink($pathDestinationFile);
        }
    }

    /**
     * @throws Exception
     * @throws LoggerException
     */
    public function testZipDestinationFileIsDirectory(): void
    {
        $path = __DIR__ . '/Fixtures/data/files/zip';
        $pathTmp = HelperFile::getGlobalTmpPath(true);

        self::assertFalse(HelperZip::zip($path, $pathTmp));
    }

    /**
     * @throws Exception
     * @throws LoggerException
     */
    public function testUnZip(): void
    {
        self::markTestSkipped();

//        $path = __DIR__ . '/Fixtures/data/files/unzip/to-be-unzipped.zip';
//        $pathToCompare = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
//        $pathTmp = HelperFile::getGlobalTmpPath(true);
//        $pathDestinationFolder = $pathTmp . DIRECTORY_SEPARATOR . 'unzip';
//
//        if (!is_dir($pathDestinationFolder)) {
//            mkdir($pathDestinationFolder);
//        }
//
//        HelperZip::unzip($path, $pathDestinationFolder);
//
        // @todo correct assertion: must compare directory contents, not file-contents
//        self::assertFileEquals($pathToCompare, $pathDestinationFolder);
//
//        if (is_dir($pathDestinationFolder)) {
//            HelperFile::rmdirRecursive($pathDestinationFolder);
//        }
    }

    /**
     * @throws Exception
     * @throws LoggerException
     */
    public function testGetContainedFileContents(): void
    {
        self::markTestIncomplete('@todo: Review and correct test and rel. method');

//        self::assertStringEqualsFile(
//            __DIR__ . '/Fixtures/data/files/unzip/unzipped/03.txt',
//            HelperZip::getContainedFileContents(
//                __DIR__ . '/Fixtures/data/files/unzip/to-be-unzipped.zip',
//                '03.txt',
//                true));
    }

    public function testGetFilepathInZip(): void
    {
        self::markTestIncomplete('@todo: Review and correct test and rel. method');

//        $path = __DIR__ . '/Fixtures/data/files/unzip/to-be-unzipped.zip';
//        $expectedFile = __DIR__ . '/Fixtures/data/files/unzip/unzipped/03.txt';
//        self::assertStringEqualsFile($expectedFile, HelperZip::getContainedFileContents($path, 'to-be-unzipped/03.txt', false));
    }
}
