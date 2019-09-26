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

use Gyselroth\Helper\HelperFile;
use Gyselroth\Helper\HelperZip;

class HelperZipTest extends HelperTestCase
{
    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Gyselroth\Helper\Exception\ZipException
     */
    public function testZipFiles()
    {
        $path  = __DIR__ . '/Fixtures/data/files/zip';
        /** @noinspection ReturnFalseInspection */
        $files = scandir($path, null);
        unset($files[0], $files[1]);
        $files = array_values($files);

        $pathTmp        = HelperFile::getGlobalTmpPath();
        $pathTmpExisted = is_dir($pathTmp);
        if (!$pathTmpExisted) {
            $pathTmpExisted = false;
            mkdir($pathTmp);
        }

        $pathDestinationFile = $pathTmp . DIRECTORY_SEPARATOR . 'tmp.zip';
        if (file_exists($pathDestinationFile)) {
            unlink($pathDestinationFile);
        }

        HelperZip::zipFiles($files, $pathDestinationFile, false, $path, false, false);

        $this->assertFileExists($pathDestinationFile);
        $this->assertGreaterThan(0, filesize($pathDestinationFile));

        $pathUnzip = $pathTmp . DIRECTORY_SEPARATOR . 'unzip';
        HelperZip::unzip($pathDestinationFile, $pathUnzip);
        $this->assertFileEquals($path, $pathUnzip);

        // Clean up
        if (file_exists($pathDestinationFile)) {
            unlink($pathDestinationFile);
        }
        if (!$pathTmpExisted) {
            HelperFile::rmdirRecursive($pathTmp);
        }
        if (is_dir($pathUnzip)) {
            HelperFile::rmdirRecursive($pathUnzip);
        }
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testZip()
    {
        $path = __DIR__ . '/Fixtures/data/files/zip/01.pdf';

        $pathTmp = HelperFile::getGlobalTmpPath(true);
        $pathDestinationFile = $pathTmp . DIRECTORY_SEPARATOR . 'test.zip';

        HelperZip::zip($path, $pathDestinationFile, false);

        $this->assertFileExists($pathDestinationFile);
        $this->assertGreaterThan(0, filesize($pathDestinationFile));

        if (file_exists($pathDestinationFile)) {
            unlink($pathDestinationFile);
        }
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testZipDestinationFileIsDirectory()
    {
        $path = __DIR__ . '/Fixtures/data/files/zip';
        $pathTmp = HelperFile::getGlobalTmpPath(true);
        $this->assertFalse(HelperZip::zip($path, $pathTmp));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testUnZip()
    {
        $path = __DIR__ . '/Fixtures/data/files/unzip/tobeunzipped.zip';
        $pathToCompare = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
        $pathTmp = HelperFile::getGlobalTmpPath(true);
        $pathDestinationFolder = $pathTmp . DIRECTORY_SEPARATOR . 'unzip';
        if (!is_dir($pathDestinationFolder)) {
            mkdir($pathDestinationFolder);
        }
        HelperZip::unzip($path, $pathDestinationFolder);
        $this->assertFileEquals($pathToCompare, $pathDestinationFolder);
        if (is_dir($pathDestinationFolder)) {
            HelperFile::rmdirRecursive($pathDestinationFolder);
        }
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testGetContainedFileContents()
    {
        $path = __DIR__ . '/Fixtures/data/files/unzip/tobeunzipped.zip';
        $expectedFile = __DIR__ . '/Fixtures/data/files/unzip/unzipped/03.txt';
        $this->assertStringEqualsFile($expectedFile, HelperZip::getContainedFileContents($path, '03.txt', true));
    }

    public function testGetFilepathInZip(){
        $path = __DIR__ . '/Fixtures/data/files/unzip/tobeunzipped.zip';
        $expectedFile = __DIR__ . '/Fixtures/data/files/unzip/unzipped/03.txt';
        $this->assertStringEqualsFile($expectedFile, HelperZip::getContainedFileContents($path, 'tobeunzipped/03.txt', false));
    }
}
