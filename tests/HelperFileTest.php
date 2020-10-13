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

use Gyselroth\Helper\HelperFile;
use PHPUnit\Framework\Constraint\IsType;

class HelperFileTest extends HelperTestCase
{
    private $uploadedFileInfo,
            $uploadedFilePath;

    protected function setUp(): void
    {
        $this->uploadedFilePath = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . '01.pdf';
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

    protected function tearDown(): void
    {
        if (is_file($this->uploadedFilePath)) {
            unlink($this->uploadedFilePath);
        }
    }

    public function testGetMimes(): void
    {
        self::assertThat(
            HelperFile::MIMES,
            new IsType('array')
        );
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testGetUniqueFilename(): void
    {
        self::assertRegExp('/\d{8}-\d{6}[a-z0-9]{13}/', HelperFile::getUniqueFilename());
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testGetUniqueFilenameNoPrefix(): void
    {
        self::assertRegExp('/\d{8}-\d{6}/', HelperFile::getUniqueFilename('', true, false));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testGetUniqueFilenameLeadString(): void
    {
        self::assertRegExp('/test\d{8}-\d{6}[a-z0-9]{13}/', HelperFile::getUniqueFilename('test'));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testGetUniqueFilenameWithFileEndingNoDatePrefix(): void
    {
        self::assertRegExp('/[a-z0-9]{13}\.zip/', HelperFile::getUniqueFilename('', false, true, 'zip'));
    }

    public function testEnsureFilenamesStartWithPath(): void
    {
        $files =            ['/test/path/to/file.zip','file','file.png'];
        $filesExpected =    ['/test/path/to/file.zip','/test/path/to/file','/test/path/to/file.png'];

        $path = '/test/path/to';

        self::assertEquals($filesExpected, HelperFile::ensureFilenamesStartWithPath($files, $path));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testValidateFilenameWithPath(): void
    {
        self::assertSame('testfile.png', HelperFile::validateFilename('test/file.png'));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testValidateFilenameForbiddenCharEqualReplacements(): void
    {
        self::assertSame(
            'long filename.png',
            HelperFile::validateFilename('long2fil3n *ame.png', ['2', '3', ' *'], [' ', 'e', ''])
        );
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testValidateFilenameForbiddenCharUnequalReplacements(): void
    {
        self::assertSame(
            'long filename.png',
            HelperFile::validateFilename('long filename.png', [' ', 'e'], [''])
        );
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     * @expectedException        \Gyselroth\Helper\Exception\FileExceptionIllegalFilename
     */
    public function testValidateFilenameEmpty(): void
    {
        HelperFile::validateFilename('');
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     */
    public function testWrite(): void
    {
        $pathFile = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'test.txt';
        $pathFileExpected  = __DIR__ . '/Fixtures/data/files/test.txt';

        HelperFile::write($pathFile, 'test');

        self::assertFileEquals($pathFileExpected, $pathFile);

        if (is_file($pathFile)) {
            unlink($pathFile);
        }
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     */
    public function testWriteAppendMode(): void
    {
        $pathFile = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'test.txt';
        $pathFileTemplate  = __DIR__ . '/Fixtures/data/files/test.txt';

        copy($pathFileTemplate, $pathFile);
        HelperFile::write($pathFile, 'file', 'a');

        self::assertStringEqualsFile($pathFile, 'testfile');

        if (\is_file($pathFile)) {
            \unlink($pathFile);
        }
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     */
    public function testWriteJson(): void
    {
        $pathFile = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'test.json';

        $array = [
            'testkey' => 'value',
            'testkey2' => 'value2'
        ];

        HelperFile::writeJson($pathFile, $array);

        self::assertStringEqualsFile($pathFile, '{"testkey":"value","testkey2":"value2"}');

        if (\is_file($pathFile)) {
            \unlink($pathFile);
        }
    }

    public function testWriteCsv(): void
    {
        $pathFile = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'test.csv';

        $header = ['Name',  'Beschäftigung', 'Firma'];

        $data = [
            ['Kay',   'Fulltime',      'Gyselroth'],
            ['Ewald', 'Parttime',      'Gyselroth']
        ];

        HelperFile::writeCsv($data, $header, $pathFile);

        self::assertStringEqualsFile(
            $pathFile,
            "Name,Beschäftigung,Firma\nKay,Fulltime,Gyselroth\nEwald,Parttime,Gyselroth\n",
            'Writing a csv file appends an empty new line.'
        );

        if (\is_file($pathFile)) {
            \unlink($pathFile);
        }
    }

//    public function testScanDirFilter(): void
//    {
//        $path = __DIR__ . '/Fixtures/data/files/unzip';
//        $array = HelperFile::scanDir($path, 'zip');
//        self::assertCount(1, $array);
//        self::assertStringEndsWith('/data/files/unzip/to-be-unzipped.zip', $array[0]);
//    }

//    public function testScanDirRecursive(): void
//    {
//        $path = __DIR__ . '/Fixtures/data/files/unzip';
//        $array = HelperFile::scanDir($path, '', true);
//
//        self::assertContains('/data/files/unzip/unzipped/01.pdf', $array);
//
//        self::assertStringEndsWith('/data/files/unzip/to-be-unzipped.zip', $array[9]);
//    }

//    public function testScanDirFindsFiles(): void
//    {
//        $files = HelperFile::scanDir(__DIR__ . '/Fixtures/data/files/unzip', '', true);
//
//        self::assertStringEndsWith('/data/files/unzip/to-be-unzipped.zip', $files[0]);
//    }

    public function testScanDirRecursiveFindsCorrectAmount(): void
    {
        self::assertCount(
            9,
            HelperFile::scanDir(__DIR__ . '/Fixtures/data/files/zip', '', true)
        );
    }

    public function testScanDirByVersionPrefixAndNaturallySort(): void
    {
        self::markTestSkipped('Purpose of function unclear');
    }

    public function testChmodRecursive(): void
    {
        $path = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'folder';

        if (!is_dir($path)) {
            mkdir($path);
        }

        $file = $path . DIRECTORY_SEPARATOR . '02.pdf';
        $pathCopy = __DIR__ . '/Fixtures/data/files/unzip/unzipped';

        HelperFile::copyDirectory($pathCopy, $path);
        HelperFile::chmodRecursive($path);

        if ($file) {
            self::assertFileIsWritable($file);
        } else {
            self::assertFileNotIsWritable($file);
        }

        HelperFile::chmodRecursive($path, '0600');

        if (is_dir($path)) {
            HelperFile::rmdirRecursive($path);
        }
    }

    public function testCopyDirectory(): void
    {
        self::markTestIncomplete();
    }

    public function testSortByDepth(): void
    {
        $files = ['intranet.test.js', 'intranet.test.sub.js', 'intranet.js', 'intranet.othertest.js'];
        $sortedFiles = ['intranet.js', 'intranet.othertest.js', 'intranet.test.js', 'intranet.test.sub.js'];

        self::assertEquals($sortedFiles, HelperFile::sortByDepth($files));
    }

    public function testGetDirectoryInfo(): void
    {
        $path = __DIR__ . '/Fixtures/data/files/unzip/unzipped';

        $expected = [
            'items' => 9,
            'size' => 36864
        ];

        self::assertEquals($expected, HelperFile::getDirectoryInfo($path));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     */
    public function testGetUploadFileInfo(): void
    {
        self::assertSame('PDF document, version 1.4', HelperFile::getUploadFileInfo($this->uploadedFileInfo));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     */
    public function testValidateUploadFileOk(): void
    {
        self::assertSame('', HelperFile::validateUploadFile($this->uploadedFileInfo, ['application/pdf']));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     */
    public function testValidateUploadFileMissingName(): void
    {
        $uploadedFileInfo = $this->uploadedFileInfo;

        unset($uploadedFileInfo['name']);

        self::assertSame(
            'Name der Datei wurde nicht empfangen',
            HelperFile::validateUploadFile($uploadedFileInfo, [])
        );
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     */
    public function testValidateUploadFileTooLarge(): void
    {
        self::assertSame(
            'Die Datei ist zu gross',
            HelperFile::validateUploadFile($this->uploadedFileInfo, [], 12091)
        );
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     */
    public function testValidateUploadFileNotAllowed(): void
    {
        self::assertSame(
            'Dateityp ist nicht erlaubt',
            HelperFile::validateUploadFile($this->uploadedFileInfo, ['image/png', 'image/jpg'])
        );
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\HelperLog\Exception\LoggerException
     */
    public function testValidateUploadFileErrors(): void
    {
        $uploadedFileInfo = $this->uploadedFileInfo;
        $uploadedFileInfo['error'] = UPLOAD_ERR_NO_FILE;
        self::assertSame('No file sent.', HelperFile::validateUploadFile($uploadedFileInfo, []));

        $uploadedFileInfo['error'] = UPLOAD_ERR_FORM_SIZE;
        self::assertSame('Exceeded filesize limit.', HelperFile::validateUploadFile($uploadedFileInfo, []));

        $uploadedFileInfo['error'] = UPLOAD_ERR_INI_SIZE;
        self::assertSame('Exceeded filesize limit.', HelperFile::validateUploadFile($uploadedFileInfo, []));

        $uploadedFileInfo['error'] = 123456789;
        self::assertSame('Unknown errors.', HelperFile::validateUploadFile($uploadedFileInfo, []));

        $uploadedFileInfo['size'] = 0;
        self::assertSame('File is empty', HelperFile::validateUploadFile($uploadedFileInfo, []));
    }

    public function testStoreUploadFile(): void
    {
        self::markTestIncomplete('Simulate actual file upload');
    }

    public function testUpload(): void
    {
        self::markTestSkipped('Function only used once, procedure unclear');
    }

    public function testUnlinkFiles(): void
    {
        $pathCopy = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
        $path = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'unlink_folder';

        if (!is_dir($path)) {
            mkdir($path);
        }

        HelperFile::copyDirectory($pathCopy, $path);
        HelperFile::unlinkFiles($path, ['01.pdf', '03.txt']);

        self::assertFileNotExists($path . DIRECTORY_SEPARATOR . '01.pdf');
        self::assertFileNotExists($path . DIRECTORY_SEPARATOR . '03.txt');
    }

    public function testDeleteFilesInDirectory(): void
    {
        $pathCopy = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
        $path = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'delete_folder';

        if (!is_dir($path)) {
            mkdir($path);
        }

        HelperFile::copyDirectory($pathCopy, $path);
        HelperFile::deleteFilesInDirectory($path . DIRECTORY_SEPARATOR . '03.*');

        self::assertFileNotExists($path . DIRECTORY_SEPARATOR . '03.pdf');
        self::assertFileNotExists($path . DIRECTORY_SEPARATOR . '03.txt');
    }

    /**
     *
     */
    public function testDeleteIfExists(): void
    {
        $pathCopy = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
        $path = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'delete_exists';

        if (!is_dir($path)) {
            mkdir($path);
        }

        $subPath = $path . DIRECTORY_SEPARATOR . 'subfolder';

        if (!is_dir($subPath)) {
            mkdir($subPath);
        }

        HelperFile::copyDirectory($pathCopy, $path);
        HelperFile::copyDirectory($pathCopy, $subPath);

        self::assertFalse(HelperFile::deleteIfExists($path . DIRECTORY_SEPARATOR . 'asdfasdf.pdf'));

        HelperFile::deleteIfExists($path . DIRECTORY_SEPARATOR . '01.pdf');

        self::assertFileNotExists($path . DIRECTORY_SEPARATOR . '01.pdf');

        HelperFile::deleteIfExists(['03.pdf', '02.txt'], $path);

        self::assertFileNotExists($path . DIRECTORY_SEPARATOR . '03.pdf');
        self::assertFileNotExists($path . DIRECTORY_SEPARATOR . '02.txt');

        HelperFile::deleteIfExists($path);

        self::assertDirectoryNotExists($subPath);
        self::assertDirectoryNotExists($path);
    }

    public function testRmdirRecursive(): void
    {
        $pathCopy = __DIR__ . '/Fixtures/data/files/unzip/unzipped';

        $path = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'remove_recursive';
        $subPath = $path . DIRECTORY_SEPARATOR . 'subfolder';

        $secondPath = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'remove_recursive2';

        if (!\is_dir($path)) {
            \mkdir($path);
        }

        if (!\is_dir($subPath)) {
            \mkdir($subPath);
        }

        if (!\is_dir($secondPath)) {
            \mkdir($secondPath);
        }

        HelperFile::copyDirectory($pathCopy, $path);
        HelperFile::copyDirectory($pathCopy, $subPath);
        HelperFile::rmdirRecursive([$path, $secondPath]);

        self::assertDirectoryNotExists($subPath);
        self::assertDirectoryNotExists($path);
        self::assertDirectoryNotExists($secondPath);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendFileHeaders(): void
    {
        self::markTestSkipped('Only testable when xdebug installed: $headers = xdebug_get_headers()');
    }

    public function testCalcBytesSize(): void
    {
        self::markTestSkipped('Already tested in HelperNumericTest');
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionPathNotFound
     */
    public function testIsDirectoryWritable(): void
    {
        $path = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'writable';

        self::assertTrue(HelperFile::isDirectoryWritable($path, false, true));
        self::assertDirectoryExists($path);
    }

    /**
     * @throws \Gyselroth\Helper\Exception\FileExceptionPathNotFound
     * @expectedException        \Gyselroth\Helper\Exception\FileExceptionPathNotFound
     */
    public function testIsDirectoryWritableException(): void
    {
        self::assertFalse(HelperFile::isDirectoryWritable('/fasdfas/vggdfgff', true, false));
    }

    public function testSanitizeFilename(): void
    {
        $input  = "thIs__wøn't--BÊ thë_såme \n filename Æfter replaÇing&prÕcessing_thiš...file";
        $output1 = 'this_wont-b-the_same-filename-fter-replaing-and-prcessing_this.file';

        self::assertSame($output1, HelperFile::sanitizeFilename($input));

        $output2 = 'thIs_wont-BE-the_same-filename-After-replaCing-and-prOcessing_this.file';
        self::assertSame($output2, HelperFile::sanitizeFilename($input, false));
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

        self::assertEquals($basenamesUnique, HelperFile::getBasenames($filePaths));
        self::assertEquals($basenames, HelperFile::getBasenames($filePaths, false));
    }

    public function testEnsurePathEndsWithDirectorySeparator(): void
    {
        self::assertSame('/this/is/a/path/', HelperFile::ensurePathEndsWithDirectorySeparator('/this/is/a/path'));

        self::assertSame('/this/is/a/path/', HelperFile::ensurePathEndsWithDirectorySeparator('/this/is/a/path/'));

        self::markTestIncomplete(
            'Next assertion skipped: HelperFile::ensurePathEndsWithDirectorySeparator(\'\')'
            . ' returns \'\' instead of \'/\''
        );

        self::assertSame('/', HelperFile::ensurePathEndsWithDirectorySeparator(''), 'Should return \'/\'');
    }

    public function testScanFilesystem(): void
    {
        $pathCopy = __DIR__ . '/Fixtures/data/files/unzip/unzipped';
        $path = HelperFile::getGlobalTmpPath(true) . DIRECTORY_SEPARATOR . 'filesystem';

        if (!is_dir($path)) {
            mkdir($path);
        }

        $subPath = $path . DIRECTORY_SEPARATOR . 'subfolder';

        if (!is_dir($subPath)) {
            mkdir($subPath);
        }

        HelperFile::copyDirectory($pathCopy, $path);
        HelperFile::copyDirectory($pathCopy, $subPath);

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

        self::assertEquals($filesystem, HelperFile::scanFilesystem($path));
        self::assertEquals($filesystemWildcard, HelperFile::scanFilesystem($path, '*.pdf'));
    }
}
