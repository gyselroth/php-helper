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

use Gyselroth\Helper\HelperConstantsFile;
use PHPUnit\Framework\Constraint\IsType;

class HelperFileTest extends HelperTestCase
{
    private $uploadedFileInfo,
            $uploadedFilePath;

    protected function setUp()
    {
        $this->uploadedFilePath = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . '01.pdf';
        $pathFileTemplate  = __DIR__ . '/Fixtures/data/files/zip/01.pdf';
        copy($pathFileTemplate, $this->uploadedFilePath);
        $this->uploadedFileInfo = [
            'name'      => '01.pdf',
            'type'      => 'application/pdf',
            'tmp_name'  => $this->uploadedFilePath,
            'error'     => 0,
            'size'      => 12092
        ];
    }

    protected function tearDown()
    {
        if (is_file($this->uploadedFilePath)) {
            unlink($this->uploadedFilePath);
        }
    }

    public function testGetMimes(): void
    {
        $this->assertThat(
            HelperConstantsFile::MIMES,
            new IsType('array')
        );
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testGetUniqueFilename(): void
    {
        $this->assertRegExp('/\d{8}-\d{6}[a-z0-9]{13}/', HelperConstantsFile::getUniqueFilename());
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testGetUniqueFilenameNoPrefix(): void
    {
        $this->assertRegExp('/\d{8}-\d{6}/', HelperConstantsFile::getUniqueFilename('', true, false));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testGetUniqueFilenameLeadString(): void
    {
        $this->assertRegExp('/test\d{8}-\d{6}[a-z0-9]{13}/', HelperConstantsFile::getUniqueFilename('test'));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testGetUniqueFilenameWithFileEndingNoDatePrefix(): void
    {
        $this->assertRegExp('/[a-z0-9]{13}\.zip/', HelperConstantsFile::getUniqueFilename('', false, true, 'zip'));
    }

    public function testEnsureFilenamesStartWithPath(): void
    {
        $files =            ['/test/path/to/file.zip','file','file.png'];
        $filesExpected =    ['/test/path/to/file.zip','/test/path/to/file','/test/path/to/file.png'];
        $path =             '/test/path/to';
        $this->assertEquals($filesExpected, HelperConstantsFile::ensureFilenamesStartWithPath($files, $path));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testValidateFilenameWithPath(): void
    {
        $this->assertSame('testfile.png', HelperConstantsFile::validateFilename('test/file.png'));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testValidateFilenameForbiddenCharEqualReplacements(): void
    {
        $this->assertSame('long filename.png',
            HelperConstantsFile::validateFilename('long2fil3n *ame.png', ['2', '3', ' *'], [' ', 'e', '']));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testValidateFilenameForbiddenCharUnequalReplacements(): void
    {
        $this->assertSame('long filename.png',
            HelperConstantsFile::validateFilename('long filename.png', [' ', 'e'], ['']));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     * @expectedException        \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testValidateFilenameEmpty(): void
    {
        HelperConstantsFile::validateFilename('');
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testWrite(): void
    {
        $pathFile = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'test.txt';
        $pathFileExpected  = __DIR__ . '/Fixtures/data/files/test.txt';
        HelperConstantsFile::write($pathFile, 'test');
        $this->assertFileEquals($pathFileExpected, $pathFile);
        if (is_file($pathFile)) {
            unlink($pathFile);
        }
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testWriteAppendMode(): void
    {
        $pathFile = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'test.txt';
        $pathFileTemplate  = __DIR__ . '/Fixtures/data/files/test.txt';
        copy($pathFileTemplate, $pathFile);
        HelperConstantsFile::write($pathFile, 'file', 'a');
        $this->assertStringEqualsFile($pathFile, 'testfile');
        if (is_file($pathFile)) {
            unlink($pathFile);
        }
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testWriteJson(): void
    {
        $pathFile = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'test.json';
        $array = [
            'testkey' => 'value',
            'testkey2' => 'value2'
        ];
        HelperConstantsFile::writeJson($pathFile, $array);
        $this->assertStringEqualsFile($pathFile, '{"testkey":"value","testkey2":"value2"}');
        if (is_file($pathFile)) {
            unlink($pathFile);
        }
    }

    public function testWriteCsv(): void
    {
        $pathFile = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'test.csv';
        $header =
            ['Name',  'Beschäftigung', 'Firma'];
        $data = [
            ['Kay',   'Fulltime',      'Gyselroth'],
            ['Ewald', 'Parttime',      'Gyselroth']
        ];
        HelperConstantsFile::writeCsv($data, $header, $pathFile);
        $this->assertStringEqualsFile($pathFile, "Name,Beschäftigung,Firma\nKay,Fulltime,Gyselroth\nEwald,Parttime,Gyselroth\n", 'Writing a csv file appends an empty new line.');
        if (is_file($pathFile)) {
            unlink($pathFile);
        }
    }

    public function testScanDir(): void
    {
        $path = __DIR__ . '/Fixtures/data/files/unzip';
        $array = HelperConstantsFile::scanDir($path);
        $this->assertCount(2, $array);
        $this->assertStringEndsWith('/data/files/unzip/tobeunzipped.zip', $array[0]);
        $this->assertStringEndsWith('/data/files/unzip/unzipped', $array[1]);
    }

    public function testScanDirFilter(): void
    {
        $path = __DIR__ . '/Fixtures/data/files/unzip';
        $array = HelperConstantsFile::scanDir($path, 'zip');
        $this->assertCount(1, $array);
        $this->assertStringEndsWith('/data/files/unzip/tobeunzipped.zip', $array[0]);
    }

    public function testScanDirRecursive(): void
    {
        $path = __DIR__ . '/Fixtures/data/files/unzip';
        $array = HelperConstantsFile::scanDir($path, '', true);
        $this->assertCount(10, $array);
        $this->assertStringEndsWith('/data/files/unzip/unzipped/01.pdf', $array[7]);
        $this->assertStringEndsWith('/data/files/unzip/tobeunzipped.zip', $array[9]);
    }

    public function testScanDirByVersionPrefixAndNaturallySort(): void
    {
        $this->markTestSkipped('Purpose of function unclear');
    }

    public function testChmodRecursive(): void
    {
        //...
        $path = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'folder';
        if (!is_dir($path)) {
            mkdir($path);
        }
        $file = $path . DIRECTORY_SEPARATOR . '02.pdf';
        $pathCopy = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
        HelperConstantsFile::copyDirectory($pathCopy, $path);
        HelperConstantsFile::chmodRecursive($path);
        $file
        ? $this->assertFileIsWritable($file)
        : $this->assertFileNotIsWritable($file);
        HelperConstantsFile::chmodRecursive($path, '0600');
        if (is_dir($path)) {
            HelperConstantsFile::rmdirRecursive($path);
        }
    }

    public function testCopyDirectory(): void
    {
        $this->markTestIncomplete();
    }

    public function testSortByDepth(): void
    {
        $files = ['intranet.test.js', 'intranet.test.sub.js', 'intranet.js', 'intranet.othertest.js'];
        $sortedFiles = ['intranet.js', 'intranet.othertest.js', 'intranet.test.js', 'intranet.test.sub.js'];
        $this->assertEquals($sortedFiles, HelperConstantsFile::sortByDepth($files));
    }

    public function testGetDirectoryInfo(): void
    {
        $path = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
        $expected = [
            'items' => 9,
            'size' => 36864
        ];
        $this->assertEquals($expected, HelperConstantsFile::getDirectoryInfo($path));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testGetUploadFileInfo(): void
    {
        $this->assertSame('PDF document, version 1.4', HelperConstantsFile::getUploadFileInfo($this->uploadedFileInfo));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testValidateUploadFileOk(): void
    {
        $this->assertSame('', HelperConstantsFile::validateUploadFile($this->uploadedFileInfo, ['application/pdf']));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testValidateUploadFileMissingName(): void
    {
        $uploadedFileInfo = $this->uploadedFileInfo;
        unset($uploadedFileInfo['name']);
        $this->assertSame('Name der Datei wurde nicht empfangen', HelperConstantsFile::validateUploadFile($uploadedFileInfo, []));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testValidateUploadFileTooLarge(): void
    {
        $this->assertSame('Die Datei ist zu gross', HelperConstantsFile::validateUploadFile($this->uploadedFileInfo, [], 12091));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testValidateUploadFileNotAllowed(): void
    {
        $this->assertSame('Dateityp ist nicht erlaubt', HelperConstantsFile::validateUploadFile($this->uploadedFileInfo, ['image/png', 'image/jpg']));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     */
    public function testValidateUploadFileErrors(): void
    {
        $uploadedFileInfo = $this->uploadedFileInfo;
        $uploadedFileInfo['error'] = UPLOAD_ERR_NO_FILE;
        $this->assertSame('No file sent.', HelperConstantsFile::validateUploadFile($uploadedFileInfo, []));
        $uploadedFileInfo['error'] = UPLOAD_ERR_FORM_SIZE;
        $this->assertSame('Exceeded filesize limit.', HelperConstantsFile::validateUploadFile($uploadedFileInfo, []));
        $uploadedFileInfo['error'] = UPLOAD_ERR_INI_SIZE;
        $this->assertSame('Exceeded filesize limit.', HelperConstantsFile::validateUploadFile($uploadedFileInfo, []));
        $uploadedFileInfo['error'] = 123456789;
        $this->assertSame('Unknown errors.', HelperConstantsFile::validateUploadFile($uploadedFileInfo, []));
        $uploadedFileInfo['size'] = 0;
        $this->assertSame('File is empty', HelperConstantsFile::validateUploadFile($uploadedFileInfo, []));
    }

    public function testStoreUploadFile(): void
    {
        $this->markTestIncomplete('Simulate actual file upload');
    }

    public function testUpload(): void
    {
        $this->markTestSkipped('Function only used once, procedure unclear');
    }

    public function testUnlinkFiles(): void
    {
        $pathCopy = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
        $path = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'unlink_folder';
        if (!is_dir($path)) {
            mkdir($path);
        }
        HelperConstantsFile::copyDirectory($pathCopy, $path);
        HelperConstantsFile::unlinkFiles($path, ['01.pdf', '03.txt']);
        $this->assertFileNotExists($path . DIRECTORY_SEPARATOR . '01.pdf');
        $this->assertFileNotExists($path . DIRECTORY_SEPARATOR . '03.txt');
    }

    public function testDeleteFilesInDirectory(): void
    {
        $pathCopy = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
        $path = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'delete_folder';
        if (!is_dir($path)) {
            mkdir($path);
        }
        HelperConstantsFile::copyDirectory($pathCopy, $path);
        HelperConstantsFile::deleteFilesInDirectory($path . DIRECTORY_SEPARATOR . '03.*');
        $this->assertFileNotExists($path . DIRECTORY_SEPARATOR . '03.pdf');
        $this->assertFileNotExists($path . DIRECTORY_SEPARATOR . '03.txt');
    }

    /**
     *
     */
    public function testDeleteIfExists()
    {
        $pathCopy = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
        $path = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'delete_exists';
        if (!is_dir($path)) {
            mkdir($path);
        }
        $subPath = $path . DIRECTORY_SEPARATOR . 'subfolder';
        if (!is_dir($subPath)) {
            mkdir($subPath);
        }
        HelperConstantsFile::copyDirectory($pathCopy, $path);
        HelperConstantsFile::copyDirectory($pathCopy, $subPath);
        $this->assertFalse(HelperConstantsFile::deleteIfExists($path . DIRECTORY_SEPARATOR . 'asdfasdf.pdf'));
        HelperConstantsFile::deleteIfExists($path . DIRECTORY_SEPARATOR . '01.pdf');
        $this->assertFileNotExists($path . DIRECTORY_SEPARATOR . '01.pdf');
        HelperConstantsFile::deleteIfExists(['03.pdf', '02.txt'], $path);
        $this->assertFileNotExists($path . DIRECTORY_SEPARATOR . '03.pdf');
        $this->assertFileNotExists($path . DIRECTORY_SEPARATOR . '02.txt');
        HelperConstantsFile::deleteIfExists($path);
        $this->assertDirectoryNotExists($subPath);
        $this->assertDirectoryNotExists($path);
    }

    public function testRmdirRecursive(): void
    {
        $pathCopy = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
        $path = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'remove_recursive';
        $subPath = $path . DIRECTORY_SEPARATOR . 'subfolder';
        $secondPath = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'remove_recursive2';
        if (!is_dir($path)) {
            mkdir($path);
        }
        if (!is_dir($subPath)) {
            mkdir($subPath);
        }
        if (!is_dir($secondPath)) {
            mkdir($secondPath);
        }
        HelperConstantsFile::copyDirectory($pathCopy, $path);
        HelperConstantsFile::copyDirectory($pathCopy, $subPath);
        HelperConstantsFile::rmdirRecursive([$path, $secondPath]);
        $this->assertDirectoryNotExists($subPath);
        $this->assertDirectoryNotExists($path);
        $this->assertDirectoryNotExists($secondPath);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendFileHeaders(): void
    {
        $this->markTestSkipped('Only testable when xdebug installed: $headers = xdebug_get_headers()');
    }

    public function testCalcBytesSize(): void
    {
        $this->markTestSkipped('Already tested in HelperNumericTest');
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionPathNotFound
     */
    public function testIsDirectoryWritable(): void
    {
        $path = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'writable';
        $this->assertTrue(HelperConstantsFile::isDirectoryWritable($path, false, true));
        $this->assertDirectoryExists($path);
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionPathNotFound
     * @expectedException        \Gyselroth\Helper\Exception\FileExceptionPathNotFound
     */
    public function testIsDirectoryWritableException(): void
    {
        $this->assertFalse(HelperConstantsFile::isDirectoryWritable('/fasdfas/vggdfgff', true, false));
    }

    public function testSanitizeFilename(): void
    {
        $input  = "thIs__wøn't--BÊ thë_såme \n filename Æfter replaÇing&prÕcessing_thiš...file";
        $output1 = 'this_wont-b-the_same-filename-fter-replaing-and-prcessing_this.file';
        $this->assertSame($output1, HelperConstantsFile::sanitizeFilename($input));
        $output2 = 'thIs_wont-BE-the_same-filename-After-replaCing-and-prOcessing_this.file';
        $this->assertSame($output2, HelperConstantsFile::sanitizeFilename($input, false));
    }

    public function testGetBasenames(): void
    {
        $filePaths = [
            '/path/to/file1.png',
            'path/to/file1.png',
            '/path/to/file2',
            '/other/path/to_other_file.longfileending',
            '/yet/another/path/with/two/slashes//in/it.pdf'
        ];
        $basenames = [
            'file1.png',
            'file1.png',
            'file2',
            'to_other_file.longfileending',
            'it.pdf'
        ];
        $basenamesUnique = [
            'file1.png',
            'file2',
            'to_other_file.longfileending',
            'it.pdf'
        ];
        $this->assertEquals($basenamesUnique, HelperConstantsFile::getBasenames($filePaths));
        $this->assertEquals($basenames, HelperConstantsFile::getBasenames($filePaths, false));
    }

    public function testEnsurePathEndsWithDirectorySeparator(): void
    {
        $this->assertSame('/this/is/a/path/', HelperConstantsFile::ensurePathEndsWithDirectorySeparator('/this/is/a/path'));
        $this->assertSame('/this/is/a/path/', HelperConstantsFile::ensurePathEndsWithDirectorySeparator('/this/is/a/path/'));
        $this->markTestIncomplete('Next assertion skipped: HelperConstantsFile::ensurePathEndsWithDirectorySeparator(\'\') returns \'\' instead of \'/\'');
        $this->assertSame('/', HelperConstantsFile::ensurePathEndsWithDirectorySeparator(''), 'Should return \'/\'');
    }

    public function testScanFilesystem(): void
    {
        $pathCopy = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
        $path = HelperConstantsFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'filesystem';
        if (!is_dir($path)) {
            mkdir($path);
        }
        $subPath = $path . DIRECTORY_SEPARATOR . 'subfolder';
        if (!is_dir($subPath)) {
            mkdir($subPath);
        }
        HelperConstantsFile::copyDirectory($pathCopy, $path);
        HelperConstantsFile::copyDirectory($pathCopy, $subPath);
        $filesystem = [
            $path . '/01.pdf',
            $path . '/01.txt',
            $path . '/02.pdf',
            $path . '/02.txt',
            $path . '/03.pdf',
            $path . '/03.txt',
            $path . '/landscape.pdf',
            $path . '/multipage-landscape.pdf',
            $path . '/multipage-portrait.pdf',
            $path . '/subfolder'
        ];
        $filesystemWildcard = [
            $path . '/01.pdf',
            $path . '/02.pdf',
            $path . '/03.pdf',
            $path . '/landscape.pdf',
            $path . '/multipage-landscape.pdf',
            $path . '/multipage-portrait.pdf'
        ];
        $this->assertEquals($filesystem, HelperConstantsFile::scanFilesystem($path));
        $this->assertEquals($filesystemWildcard, HelperConstantsFile::scanFilesystem($path, '*.pdf'));
    }
}
