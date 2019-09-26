<?php

/**
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Gyselroth\Helper;

use finfo;
use Gyselroth\Helper\Exception\FileException;
use Gyselroth\Helper\Exception\FileExceptionFailedTransfer;
use Gyselroth\Helper\Exception\FileExceptionIllegalFilename;
use Gyselroth\Helper\Exception\FileExceptionInvalidFile;
use Gyselroth\Helper\Exception\FileExceptionInvalidPath;
use Gyselroth\Helper\Exception\FileExceptionPathNotFound;
use Gyselroth\Helper\Interfaces\ConstantsFileTypesInterface;
use Gyselroth\Helper\Interfaces\ConstantsMimeTypesInterface;

class HelperFile implements ConstantsFileTypesInterface, ConstantsMimeTypesInterface
{
    public const LOG_CATEGORY = 'fs';

    public const FILE_MODE_GRANT_ALL = 0777;

    public const MIMES = [
        self::MIME_TYPE_HTML => 'code',
        self::MIME_TYPE_IMAGE_GIF => 'image',
        self::MIME_TYPE_IMAGE_JPEG => 'image',
        self::MIME_TYPE_IMAGE_PNG => 'image',
        self::MIME_TYPE_PDF => 'pdf',
        self::MIME_TYPE_TEXT => 'file',

        self::MIME_TYPE_MS_WORD => 'word',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'word',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => 'word',
        'application/vnd.ms-word.document.macroEnabled.12' => 'word',
        'application/vnd.ms-word.template.macroEnabled.12' => 'word',
        'application/vnd.oasis.opendocument.text' => 'word',
        self::MIME_TYPE_MS_EXCEL => 'excel',
        'application/vnd.ms-excel' => 'excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => 'excel',
        'application/vnd.ms-excel.sheet.macroEnabled.12' => 'excel',
        'application/vnd.ms-excel.template.macroEnabled.12' => 'excel',
        'application/vnd.ms-excel.addin.macroEnabled.12' => 'excel',
        'application/vnd.ms-excel.sheet.binary.macroEnabled.12' => 'excel',
        self::MIME_TYPE_MS_POWERPOINT => 'powerpoint',
        'application/vnd.ms-powerpoint' => 'powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.template' => 'powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => 'powerpoint',
        'application/vnd.ms-powerpoint.addin.macroEnabled.12' => 'powerpoint',
        'application/vnd.ms-powerpoint.presentation.macroEnabled.12' => 'powerpoint',
        'application/vnd.ms-powerpoint.template.macroEnabled.12' => 'powerpoint',
        'application/vnd.ms-powerpoint.slideshow.macroEnabled.12' => 'powerpoint',
        self::MIME_TYPE_CSV => 'csv',
        'text/comma-separated-values' => 'csv',
        'text/x-comma-separated-values' => 'csv',
        'application/csv' => 'csv',
        self::MIME_TYPE_MS_ACCESS => 'access',

        self::MIME_TYPE_ZIP => self::FILE_ENDING_ZIP
    ];

    private const DEFAULT_UPLOAD_ALLOWED_MIME_TYPES = [
        self::MIME_TYPE_TEXT,
        self::MIME_TYPE_PDF,
        self::MIME_TYPE_MS_WORD,
        self::MIME_TYPE_VND_OPEN_XML_WORD
    ];

    /**
     * @var  array
     */
    public $mimes = self::MIMES;

    /** @var string */
    public static $rootPath;

    /**
     * @return string
     * @singleton
     */
    public static function getRootPath(): string
    {
        if (empty(self::$rootPath)) {
            /** @noinspection ReturnFalseInspection */
            $pathDelimiter = false !== \strpos(__DIR__, '/vendor/')
                // __DIR__ is e.g. '/srv/www/trunk/vendor/gyselroth/....../HelperFile
                ? '/vendor/'

                // Fallback: helper-package seems to be itself the project at hand (is not installed in one of composer's vendor sub directories)
                // __DIR__ is e.g. '/srv/www/trunk/src/Gyselroth/Helper'
                : '/src/';

            self::$rootPath = \explode($pathDelimiter, __DIR__)[0];
        }

        return self::$rootPath;
    }

    /**
     * @param  bool $createIfNotExists
     * @return string
     * @throws \RuntimeException
     */
    public static function getGlobalTmpPath($createIfNotExists = false): string
    {
        $path = self::getRootPath() . '/tmp';
        if ($createIfNotExists
            && !\is_dir($path)
            && !\mkdir($path)
        ) {
            throw new \RuntimeException(\sprintf('Failed create directory "%s"', $path));
        }

        return $path;
    }

    /**
     * @param  string $pathFile
     * @return string|bool
     */
    private static function getMimeType(string $pathFile)
    {
        return \finfo_file(\finfo_open(FILEINFO_MIME_TYPE), $pathFile);
    }

    /**
     * @param  string $leadStr               Default: ''
     * @param  bool   $prefixWithDateTime    Default: true
     * @param  bool   $generateUniquePostfix Default: true
     * @param  string $fileExtension         Default: ''
     * @return string  filename
     * @throws FileExceptionIllegalFilename
     */
    public static function getUniqueFilename(
        string $leadStr = '',
        bool $prefixWithDateTime = true,
        bool $generateUniquePostfix = true,
        string $fileExtension = ''
    ): string
    {
        $prefix = $prefixWithDateTime ? \date('Ymd-His') : '';

        $filename = $leadStr .
            ($generateUniquePostfix
                ? \uniqid($prefix, false)
                : $prefix) . (empty($fileExtension) ? '' : '.' . $fileExtension);

        return self::validateFilename($filename);
    }

    public static function ensureFilenamesStartWithPath(array $files, string $path): array
    {
        if ('' === $path) {
            return $files;
        }

        $path = self::ensurePathEndsWithDirectorySeparator($path);
        foreach ($files as $index => $file) {
            if (!HelperString::startsWith($file, $path)) {
                $files[$index] = $path . $file;
            }
        }

        return $files;
    }

    /**
     * Remove illegal characters from filename
     *
     * @param  string $filename
     * @param  array  $forbiddenChars
     * @param  array  $replacementChars
     * @return string
     * @throws FileExceptionIllegalFilename
     */
    public static function validateFilename(
        string $filename,
        array $forbiddenChars = [],
        array $replacementChars = []
    ): string
    {
        $filename = \str_replace(DIRECTORY_SEPARATOR, '', $filename);

        if (empty($filename)) {
            throw new FileExceptionIllegalFilename('Filename cannot be empty');
        }

        $amountForbiddenChars = \count($forbiddenChars);
        if ($amountForbiddenChars > 0
            && $amountForbiddenChars === \count($replacementChars)
        ) {
            $filename = \str_replace($forbiddenChars, $replacementChars, $filename);
        }

        return \trim($filename);
    }

    /**
     * @param  string $pathFile
     * @param  string $content
     * @param  string $mode
     * @return bool
     * @throws \Exception
     */
    public static function write(string $pathFile, string $content, string $mode = 'w'): bool
    {
        $handle = \fopen($pathFile, $mode);
        if (!$handle) {
            LoggerWrapper::error('fopen failed: ' . $pathFile);
            return false;
        }

        \fwrite($handle, $content);

        return \fclose($handle);
    }

    /**
     * @param  string       $filePath
     * @param  array|string $content
     * @return bool
     * @throws \Exception
     */
    public static function writeJson(string $filePath, $content): bool
    {
        if (\is_array($content)) {
            $content = \json_encode($content);
        }

        return self::write($filePath, $content);
    }

    public static function writeCsv(array $rows, array $headerFields, string $filePath): bool
    {
        if ([] === $rows) {
            return false;
        }

        \ini_set('auto_detect_line_endings', true);

        $handle = \fopen($filePath, 'wb');
        \fputcsv($handle, $headerFields);
        foreach ($rows as $row) {
            \fputcsv($handle, $row);
            unset($row);
        }

        return fclose($handle);
    }

    /**
     * Get list of files in given directory, optional recursive, optionally filtered: only files w/ filename containing the given substring
     *
     * @param  string $path
     * @param  string $ext        Optional: limit by file extension or trailing string
     * @param  bool   $recursive
     * @param  string $leadString Optional: limit to files beginning w/ this string
     * @param  bool   $namesOnly
     * @return array  Matched files (including their full path), or FALSE if path invalid
     */
    public static function scanDir(
        string $path,
        string $ext = '',
        bool $recursive = false,
        string $leadString = '',
        bool $namesOnly = false
    ): array
    {
        if (!\is_dir($path)) {
            return [];
        }

        if ($recursive) {
            return self::scanDirRecursive($path, $ext, $leadString);
        }

        /** @noinspection ScandirUsageInspection */
        /** @noinspection ReturnFalseInspection */
        $files = scandir($path);

        // Filter: 1. remove '.' and '..', 2. by extension (or substring), 3. by lead string
        $filesFiltered = [];
        foreach ($files as $file) {
            if ('.' !== $file
                && '..' !== $file
                && (empty($ext) || HelperString::endsWith($file, $ext))
                && (empty($leadString) || HelperString::startsWith(\basename($file), $leadString))
            ) {
                $filesFiltered[] = $file;
            }
        }

        if (!$namesOnly) {
            foreach ($filesFiltered as &$file) {
                // Prepend filename w/ path
                $file = $path . DIRECTORY_SEPARATOR . $file;
            }
        }

        return $filesFiltered;
    }

    public static function scanDirByVersionPrefixAndNaturallySort(
        string $path,
        string $ext = '',
        bool $recursive = false,
        string $leadString = ''
    ): array
    {
        $filesFiltered = self::scanDir($path, $ext, $recursive, $leadString);
        \natsort($filesFiltered);

        $recentFiles = [];
        foreach ($filesFiltered as $filePath) {
            $explodedPath           = \explode(DIRECTORY_SEPARATOR, $filePath);
            $fileName               = \end($explodedPath);
            $fileNameWithoutVersion = \preg_replace('/_v(.*)/', '', $fileName);

            $recentFiles[$fileNameWithoutVersion] = $filePath;
        }

        return \array_values($recentFiles);
    }

    /**
     * @param  string $path
     * @param  string $ext        Optional: limit by file extension or trailing string
     * @param  string $leadString Optional: limit to files beginning w/ this string
     * @return array              Filenames including the full path
     */
    public static function scanDirRecursive(string $path, string $ext = '', string $leadString = ''): array
    {
        $items = [];
        if ($handle = \opendir($path)) {
            while (false !== ($file = \readdir($handle))) {
                $pathFile = $path . DIRECTORY_SEPARATOR . $file;
                if (0 === \preg_match('/^(^\.)/', $file)) {
                    if (\is_dir($pathFile)) {
                        /** @noinspection SlowArrayOperationsInLoopInspection */
                        $items = \array_merge($items, self::scanDirRecursive($pathFile, $ext, $leadString));
                    } elseif (!$ext || HelperString::endsWith($pathFile, $ext)
                        && (
                            empty($leadString)
                            || HelperString::startsWith(\basename($pathFile), $leadString)
                        )
                    ) {
                        $items[] = \preg_replace('/\/\//', DIRECTORY_SEPARATOR, $pathFile);
                    }
                }
            }

            \closedir($handle);
        }

        return $items;
    }

    /**
     * @param  string $path
     * @param  int    $mode
     * @return array
     */
    public static function chmodRecursive(string $path, $mode = self::FILE_MODE_GRANT_ALL): array
    {
        if (\file_exists($path)) {
            \chmod($path, $mode);
        }
        $items = [];
        if (\is_dir($path)
            && $handle = \opendir($path)
        ) {
            while (false !== ($file = \readdir($handle))) {
                $pathFile = $path . DIRECTORY_SEPARATOR . $file;
                if (0 === \preg_match('/^(^\.)/', $file)) {
                    \chmod($pathFile, $mode);

                    if (\is_dir($pathFile)) {
                        /** @noinspection SlowArrayOperationsInLoopInspection */
                        $items = \array_merge($items, self::chmodRecursive($pathFile, $mode));
                    }
                }
            }

            \closedir($handle);
        }

        return $items;
    }

    public static function copyDirectory(string $directoryName, string $destinationPath, bool $overwrite = true): bool
    {
        if ($overwrite) {
            self::deleteIfExists($destinationPath);
        }
        if (!\mkdir($destinationPath)) {
            return false;
        }

        $directoryContent = self::scanDir($directoryName);
        if (!$directoryContent) {
            return true;
        }
        foreach ($directoryContent as $item) {
            if (\is_file($item)) {
                \copy($item, $destinationPath . DIRECTORY_SEPARATOR . \basename($item));
            } elseif (is_dir($item)) {
                self::copyDirectory($item, $destinationPath . DIRECTORY_SEPARATOR . \basename($item));
            }
        }

        return true;
    }

    /**
     * Sort given files by "depth", that is: list less "deep" files first, sort files of identical depth alphabetically.
     * Eg: 'foo.js', 'foo.bar.js', 'foo.baz.js', 'foo.bar.baz.js'
     *
     * @param  array $files
     * @return array
     */
    public static function sortByDepth(array $files): array
    {
        \sort($files);
        \uasort($files, function ($a, $b) {
            $aCount = \substr_count($a, '.');
            $bCount = \substr_count($b, '.');

            if ($aCount === $bCount) {
                // 1. if depth is identical: sort alphabetic
                return $a < $b ? -1 : 1;
            }

            // 2. sort by depth
            return $aCount < $bCount ? -1 : 1;
        });

        return $files;
    }

    /**
     * Get amount of items and sum of bytes of files in given path
     *
     * @param  string $path
     * @param  array  $info
     * @return array            New size and unit
     */
    public static function getDirectoryInfo(string $path, array $info = ['items' => 0, 'size' => 0]): array
    {
        $children      = \glob($path . DIRECTORY_SEPARATOR . '*');
        $info['items'] += \count($children);

        foreach ($children as $item) {
            if (\is_file($item)) {
                $info['size'] += \filesize($path);
            } elseif (\is_dir($item)) {
                $depth         = self::getDirectoryInfo($item, $info);
                $info['size']  += $depth['size'];
                $info['items'] += $depth['items'];
            }
        }

        return $info;
    }

    /**
     * Helper function getting files from dir
     *
     * @param  string $path
     * @param  int    $level
     * @return array
     * @throws \Exception
     */
    public static function getDirectory(string $path = '.', int $level = 0): array
    {
        $ignore = ['cgi-bin', '.', '..', '.DS_Store', '.svn', '.git'];
        $handle = @opendir($path);

        if (false === $handle) {
            LoggerWrapper::warning(
                "Cannot open path: $path",
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]);
            return [];
        }

        $data = [];
        while (false !== ($file = \readdir($handle))) {
            if (!\in_array($file, $ignore, true)) {
                if (\is_dir("$path/$file")) {
                    $data[] = $file;
                    self::getDirectory("$path/$file", $level + 1);
                } else {
                    $data[] = $file;
                }
            }
        }

        \closedir($handle);

        return $data;
    }

    /**
     * @param  array $uploadFile
     * @param  int   $options
     * @return string
     * @throws \Exception
     */
    public static function getUploadFileInfo(array $uploadFile, int $options = FILEINFO_NONE): string
    {
        if (FILEINFO_MIME_TYPE === $options) {
            $extension = \pathinfo(\strtolower($uploadFile['name']), PATHINFO_EXTENSION);
            switch ($extension) {
                case self::FILE_ENDING_CSV:
                    return self::MIME_TYPE_CSV;
                case self::FILE_ENDING_GIF:
                    return self::MIME_TYPE_IMAGE_GIF;
                case self::FILE_ENDING_JAVASCRIPT:
                    return self::MIME_TYPE_JAVASCRIPT;
                case self::FILE_ENDING_JPG:
                case self::FILE_ENDING_JPEG:
                    return self::MIME_TYPE_IMAGE_JPEG;
                case self::FILE_ENDING_JSON:
                    return self::MIME_TYPE_JSON;
                case self::FILE_ENDING_PDF:
                    return self::MIME_TYPE_PDF;
                case self::FILE_ENDING_PNG:
                    return self::MIME_TYPE_IMAGE_PNG;
                case self::FILE_ENDING_TXT:
                    return self::MIME_TYPE_TEXT;
                case self::FILE_ENDING_XLS:
                    return self::MIME_TYPE_VND_OPEN_XML_SPREADSHEET;
                case self::FILE_ENDING_XML:
                    return self::MIME_TYPE_XML;
                case self::FILE_ENDING_ZIP:
                    return self::MIME_TYPE_ZIP;
                default:
                    LoggerWrapper::warning(
                        "Detected unhandled file extension: $extension",
                        [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY, LoggerWrapper::OPT_PARAMS => $extension]);
            }
        }

        return (new \finfo())->file($uploadFile['tmp_name'], $options);
    }

    /**
     * @param  array $uploadFile
     * @param  array $allowedTypes    Empty array = no type restriction
     * @param  int   $maximumFileSize Max. size / -1 for unlimited size
     * @return string                   Error message / empty string if valid
     * @throws \Exception
     */
    public static function validateUploadFile(
        array $uploadFile,
        array $allowedTypes,
        $maximumFileSize = 2000000
    ): string
    {
        if (!isset($uploadFile['name'])) {
            // @todo throw exception
            return HelperString::translate('Name der Datei wurde nicht empfangen');
        }
        if ($maximumFileSize > -1
            && $uploadFile['size'] > $maximumFileSize
        ) {
            // @todo throw exception
            return HelperString::translate('Die Datei ist zu gross');
        }

        $mimeType = self::getUploadFileInfo($uploadFile, FILEINFO_MIME_TYPE);
        if ([] !== $allowedTypes
            && !\in_array($mimeType, $allowedTypes, true)
        ) {
            // @todo throw exception
            return HelperString::translate('Dateityp ist nicht erlaubt');
        }

        switch ($uploadFile['error']) {
            case UPLOAD_ERR_OK:
                return '';
            case UPLOAD_ERR_NO_FILE:
                return HelperString::translate('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return HelperString::translate('Exceeded filesize limit.');
            default:
                return 0 === $uploadFile['size']
                    ? HelperString::translate('File is empty')
                    : HelperString::translate('Unknown errors.');
        }
    }

    /**
     * Validate and store upload file (given in $_FILES[userfile]) to given storage directory (which is created if not exists)
     *
     * @param  string $storagePath     Where to store the file?
     * @param  array  $allowedTypes
     * @param  int    $maximumFileSize Max. size / -1 for unlimited size
     * @param  string $storageFilename New filename for stored file, if different from original upload filename
     * @return string                    Path to copied upload file or false on any failure
     * @throws FileException
     * @throws FileExceptionFailedTransfer
     * @throws FileExceptionInvalidPath
     * @throws FileExceptionPathNotFound
     */
    public static function storeUploadFile(
        string $storagePath,
        array $allowedTypes = [],
        $maximumFileSize = 3000000,
        string $storageFilename = ''
    ): string
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $message = self::validateUploadFile($_FILES['userfile'], $allowedTypes, $maximumFileSize);
        if (!empty($message)) {
            throw new FileExceptionFailedTransfer($message);
        }
        if (!self::isDirectoryWritable($storagePath)) {
            // Storage directory does not exist / is not writable
            throw new FileExceptionInvalidPath('Invalid storage path');
        }

        $storageFilename = \trim($storageFilename);
        if (empty($storageFilename)) {
            $storageFilename = $_FILES['name'];
        }

        // Move to storage directory
        $storageFilePath = $storagePath . DIRECTORY_SEPARATOR . $storageFilename;
        if (!\move_uploaded_file($_FILES['userfile']['tmp_name'], $storageFilePath)) {
            throw new FileException('Failed to move uploaded file ().');
        }

        return $storageFilePath;
    }

    /**
     * Validate upload file and evoke given callback functions upon it
     *
     * @param  array  $file
     * @param  array  $callbackFileExists Callback function configuration (containing 'model' and 'functionName') - function to check for pre-existing file
     * @param  array  $callbackCreateFile Callback function configuration (containing 'model' and 'functionName') - function to save posted file
     * @param  string $fileNamePrefix
     * @param  array  $allowedTypes       Array of allowed MIME types, e.g: 'text/plain', 'application/pdf', ...
     * @param  int    $maximumFileSize    Max. size / -1 for unlimited size
     * @return string Filename of uploaded file
     * @throws FileException
     * @throws FileExceptionInvalidFile
     */
    public static function upload(
        array $file,
        array $callbackFileExists,
        array $callbackCreateFile,
        $fileNamePrefix = '',
        array $allowedTypes = [],
        $maximumFileSize = -1
    ): string
    {
        $filename = $fileNamePrefix . $file['name'];

        $callbackModelFileExists        = $callbackFileExists['model'];
        $callbackFunctionNameFileExists = $callbackFileExists['functionName'];

        $callbackModelCreateFile        = $callbackCreateFile['model'];
        $callbackFunctionNameCreateFile = $callbackCreateFile['functionName'];

        if ([] === $allowedTypes) {
            $allowedTypes = self::DEFAULT_UPLOAD_ALLOWED_MIME_TYPES;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $message = self::validateUploadFile($file, $allowedTypes, $maximumFileSize);
        if ('' !== $message) {
            throw new FileExceptionInvalidFile($message);
        }
        if ($callbackModelFileExists->$callbackFunctionNameFileExists($filename)) {
            throw new FileException('File already exists');
        }
        if ($callbackModelCreateFile->$callbackFunctionNameCreateFile($filename, $file['tmp_name'])) {
            return $filename;
        }

        throw new FileException('Upload failed');
    }

    public static function unlinkFiles(string $path, array $files = []): bool
    {
        foreach ($files as $file) {
            if (\file_exists($path . DIRECTORY_SEPARATOR . $file)) {
                \unlink($path . DIRECTORY_SEPARATOR . $file);
            }
        }

        return true;
    }

    /**
     * @param  string $pathPattern Path possibly containing a file pattern, e.g.: "tmp" or "tmp/*.docx"
     */
    public static function deleteFilesInDirectory(string $pathPattern): void
    {
        $files = \glob($pathPattern);
        if ([] !== $files
            && false !== $files
        ) {
            foreach ($files as $file) {
                \unlink($file);
            }
        }
    }

    /**
     * Deletes given file or directory (including all contained directories and files)
     *
     * @param  string|array $filename Filename, or $path empty: full path including filename | Array: multiple filenames
     * @param  string       $path     Path to file, optional
     * @return bool
     * @todo   log deletion failures
     */
    public static function deleteIfExists($filename, string $path = ''): bool
    {
        if (\is_array($filename)) {
            $res = true;
            /** @noinspection ForeachSourceInspection */
            foreach($filename as $filenameSingle) {
                $res = $res && self::deleteIfExists($filenameSingle, $path);
            }

            return $res;
        }

        $pathFull = ('' === $path ? '' : $path . DIRECTORY_SEPARATOR) . $filename;

        if (\file_exists($pathFull)) {
            return \is_dir($pathFull)
                ? self::rmdirRecursive($pathFull)
                : \unlink($pathFull);
        }

        return false;
    }

    /**
     * @param  string|array $path
     * @return bool
     */
    public static function rmdirRecursive($path): bool
    {
        if (\is_array($path)) {
            foreach ($path as $pathSingle) {
                self::rmdirRecursive($pathSingle);
            }
            return true;
        }

        if (!\is_dir($path)) {
            return false;
        }

        /** @noinspection ReturnFalseInspection */
        $files = \scandir($path, null);
        foreach ($files as $file) {
            if ('.' !== $file
                && '..' !== $file
            ) {
                $rmPath = $path . '/' . $file;
                if (\is_dir($rmPath)) {
                    self::rmdirRecursive($rmPath);
                } else {
                    \unlink($rmPath);
                }
            }
        }

        /** @noinspection ReturnFalseInspection */
        \reset($files);

        return \rmdir($path);
    }

    public static function sendFileHeaders(string $filename, string $contentType): void
    {
        \header('Last-Modified: ' . \gmdate('D, d M Y H:i:s') . ' GMT');
        \header('Cache-Control: no-store, no-cache, must-revalidate');
        \header('Cache-Control: post-check=0, pre-check=0', false);
        \header('Pragma: no-cache');
        \header('Content-Type: ' . $contentType);
        \header('Content-Disposition: attachment;filename="' . $filename . '"');
    }

    /**
     * Get size and unit (bytes, kilo or megabytes) values from given amount
     *
     * @param  int $bytes Size
     * @return array      Array w/ 'size' and 'unit'
     */
    public static function calcBytesSize($bytes): array
    {
        return HelperNumeric::calcBytesSize($bytes);
    }

    /**
     * Check/ensure directory at given path exists and is writable
     *
     * @param  string $path
     * @param  bool   $makeWritableIfNot
     * @param  bool   $createIfNotExists
     * @return bool
     * @throws FileExceptionPathNotFound
     */
    public static function isDirectoryWritable(
        string $path,
        bool $makeWritableIfNot = true,
        bool $createIfNotExists = true
    ): bool
    {
        if ($createIfNotExists
            && !\is_dir($path)
        ) {
            /** @noinspection MkdirRaceConditionInspection */
            \mkdir($path, self::FILE_MODE_GRANT_ALL, true);
        }
        if (!\is_dir($path)) {
            throw new FileExceptionPathNotFound('Directory does not exist');
        }
        if ($makeWritableIfNot
            && !\is_writable($path)
        ) {
            \chmod($path, self::FILE_MODE_GRANT_ALL);
        }

        return \is_dir($path)
            && \is_writable($path);
    }

    /**
     * @param  string $filename
     * @param  bool   $toLower
     * @return string
     */
    public static function sanitizeFilename(string $filename, bool $toLower = true): string
    {
        // Convert space to hyphen, remove single- and double- quotes
        $filename = \str_replace([' ', '\'', '"'], ['-', '', ''], $filename);

        $replacePairs = [
            'š' => 's', 'ð' => 'dj', 'ž' => 'z', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a', 'æ' => 'a',
            'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u',
            'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f',
            'ß' => 'ss'
        ];

        if ($toLower) {
            $filename = \strtolower($filename);
        } else {
            // Needs to translate also upper-case characters
            $replacePairs = \array_merge($replacePairs, [
                'Š' => 'S', 'Ð' => 'DJ', 'Ž' => 'Z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Æ' => 'A',
                'Ç' => 'C', 'È' => 'E',  'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
                'Ñ' => 'N', 'Ò' => 'O',  'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U',
                'Ý' => 'Y', 'Þ' => 'B',  'Ÿ' => 'Y', 'Ƒ' => 'F',
            ]);
        }

        $filename = \strtr($filename, $replacePairs);

        $filename = \str_replace(['&', '@', '#'], ['-and-', '-at-', '-number-'], $filename);

        // Remove non-word chars (leaving hyphens and periods)
        $filename = \preg_replace('/[^\w\-.]+/', '', $filename);

        // Reduce multiple hyphens to one
        $filename = \preg_replace('/[\-]+/', '-', $filename);

        return HelperString::reduceCharRepetitions($filename, ['.', '_', '-']);
    }

    public static function getBasenames(array $filePaths, bool $makeUnique = true): array
    {
        $baseNames = [];
        foreach ($filePaths as $filePath) {
            $baseNames[] = \basename($filePath);
        }

        return $makeUnique
            ? \array_values(array_unique($baseNames))
            : $baseNames;
    }

    public static function ensurePathEndsWithDirectorySeparator(string $path): string
    {
        return $path . (empty($path)
            || HelperString::endsWith($path, DIRECTORY_SEPARATOR)
                ? ''
                : DIRECTORY_SEPARATOR);
    }

    public static function scanFilesystem(string $path, string $wildcard = '*'): array
    {
        return \glob($path . DIRECTORY_SEPARATOR . $wildcard);
    }

    /**
     * @param  string $fileName
     * @param  string $fileStoredIn
     */
    public static function moveUploadedFileToTempDirectory($fileName, $fileStoredIn): void
    {
        \move_uploaded_file($fileStoredIn, PATH_TMP . DIRECTORY_SEPARATOR . $fileName);
    }

    /**
     * @deprecated  test the method, replace its usages with HelperFile::scanDir() w/ subsequent reforming and remove this additional method
     * @param  string $path
     * @return array|bool
     * @throws \Exception
     */
    public static function getXMLFilesFromDir(string $path)
    {
        $xmlFiles = self::getDirectory($path);
        if ([] === $xmlFiles) {
            return false;
        }

        \asort($xmlFiles);
        $files = [];
        foreach ($xmlFiles as $fileName) {
            $files[] = [
                'name'  => $fileName,
                'value' => $fileName
            ];
        }

        return $files;
    }
}
