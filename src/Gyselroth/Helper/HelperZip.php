<?php

/**
 * ZIP helper classes
 * Copyright (c) 2017-2020 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Gyselroth\Helper;

use Gyselroth\Helper\Exception\ZipException;
use ZipArchive;

class HelperZip
{
    public const LOG_CATEGORY = 'zip';

    // Error codes (beyond those provided in ZipArchive class)
    private const ERROR_CODE_NO_FILES          = 1;
    private const ERROR_CODE_HAS_INVALID_FILES = 2;

    /** @var bool */
    private static $isExtLoaded;

    /**
     * @return bool
     * @singleton
     */
    public static function isExtensionInstalled(): bool
    {
        return self::$isExtLoaded ?? self::$isExtLoaded = \extension_loaded('zip');
    }

    /**
     * Zip all files in given array into new archive
     *
     * @param  array  $files
     * @param  string $destinationFile
     * @param  bool   $overwrite
     * @param  string $pathFiles
     * @param  bool   $writeResultFileToSourceFilesPath
     * @param  bool   $deleteFilesWhenDone
     * @return bool|string|null
     * @throws ZipException
     */
    public static function zipFiles(
        array $files,
        string $destinationFile = '',
        bool $overwrite = false,
        string $pathFiles = '',
        $writeResultFileToSourceFilesPath = true,
        $deleteFilesWhenDone = true
    )
    {
        if (!self::isExtensionInstalled()) {
            return false;
        }

        $pathFiles = HelperFile::ensurePathEndsWithDirectorySeparator($pathFiles);

        if ($writeResultFileToSourceFilesPath) {
            $destinationFile = $pathFiles . $destinationFile;
        }

        if (!\is_array($files)
            || (\file_exists($destinationFile) && !$overwrite)
        ) {
            return false;
        }

        $validFiles = [];

        foreach ($files as $file) {
            if (\file_exists($pathFiles . $file)) {
                $validFiles[] = $pathFiles . $file;
            }
        }

        if ([] === $validFiles) {
            self::handleZipError($pathFiles, self::ERROR_CODE_NO_FILES);

            return false;
        }

        if (\count($validFiles) !== \count($files)) {
            self::handleZipError($pathFiles, self::ERROR_CODE_HAS_INVALID_FILES, LOG_WARNING);
        }

        $zip = new \ZipArchive();

        if ($overwrite
            && !\file_exists($destinationFile)
        ) {
            // @note bug in ZipArchive: https://bugs.php.net/bug.php?id=71064 -
            // open() w/ mode \ZipArchive::OVERWRITE fails if the file does not exist yet
            $overwrite = false;
        }

        $result = $zip->open($destinationFile, $overwrite
            ? \ZipArchive::OVERWRITE
            : \ZipArchive::CREATE | \ZipArchive::OVERWRITE
        );

        if (!$result) {
            self::handleZipError($destinationFile, $result);

            return false;
        }

        foreach ($validFiles as $file) {
            $zip->addFile($file, \basename($file));
        }

        $zip->close();

        if (!\is_file($destinationFile)) {
            throw new ZipException("Failed zipping to file: $destinationFile");
        }

        if ($deleteFilesWhenDone) {
            // Delete temp files
            foreach ($files as $file) {
                \unlink($pathFiles . $file);
            }
        }

        return true;
    }

    /**
     * Create ZIP archive from files in given path
     *
     * @param  string $pathUnzipped
     * @param  string $destinationFilename
     * @param  bool   $deleteUnzipped
     * @return string|false
     * @throws \Exception
     */
    public static function zip(
        string $pathUnzipped,
        string $destinationFilename,
        bool $deleteUnzipped = false
    )
    {
        if (!self::isExtensionInstalled()) {
            return false;
        }

        if (!\file_exists($pathUnzipped)) {
            LoggerWrapper::error(
                'Zipping failed: file does not exist:' . $pathUnzipped,
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
            );

            return false;
        }

        if (\is_dir($destinationFilename)) {
            LoggerWrapper::error(
                'Zipping failed: given destination is directory:' . $destinationFilename,
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
            );

            return false;
        }

        $zip = new \ZipArchive();

        if (!$zip->open($destinationFilename, \ZipArchive::CREATE)) {
            LoggerWrapper::error(
                'Zipping failed: cannot open archive:' . $pathUnzipped,
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
            );

            return false;
        }

        $realpathUnzipped = \realpath($pathUnzipped);

        if (false === $realpathUnzipped) {
            LoggerWrapper::error(
                'Zipping failed: cannot return absolute pathname' . $pathUnzipped,
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
            );
            return false;
        }

        $pathUnzipped = \str_replace('\\', DIRECTORY_SEPARATOR, $realpathUnzipped);

        if (\is_file($pathUnzipped)) {
            if (\file_exists($pathUnzipped)
                && !$zip->addFile($pathUnzipped, \basename($pathUnzipped))
            ) {
                LoggerWrapper::warning(
                    'ZipArchive->addFile failed for file: ' . $pathUnzipped,
                    [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
                );
            }
        } elseif (\is_dir($pathUnzipped)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($pathUnzipped),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            $pathDots = ['.', '..'];

            foreach ($files as $file) {
                $file = \str_replace('\\', DIRECTORY_SEPARATOR, $file);

                if (\in_array(
                        \substr($file, \strrpos($file, DIRECTORY_SEPARATOR) + 1),
                        $pathDots,
                    true
                    )
                ) {
                    // Ignore "." and ".." folders
                    continue;
                }

                $file = \realpath($file);

                if (false === $file) {
                    LoggerWrapper::error(
                        'Zipping failed: cannot return absolute pathname' . $file,
                        [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
                    );
                    return false;
                }

                if (\is_dir($file)) {
                    $zip->addEmptyDir(
                        \str_replace(
                            $pathUnzipped . DIRECTORY_SEPARATOR,
                            '',
                            $file . DIRECTORY_SEPARATOR
                        )
                    );
                } elseif (\is_file($file)) {
                    $zip->addFile(
                        $file,
                        \str_replace(
                            $pathUnzipped . DIRECTORY_SEPARATOR,
                            '',
                            $file
                        )
                    );
                }
            }
        }

        $zip->close();

        if ($deleteUnzipped) {
            HelperFile::rmdirRecursive($pathUnzipped);
        }

        return $destinationFilename;
    }

    /**
     * @param  string $pathArchive
     * @param  string $pathDestination
     * @param  bool   $ensureDestinationFilePermission
     * @return bool
     * @throws \Exception
     */
    public static function unzip(
        string $pathArchive,
        string $pathDestination,
        bool $ensureDestinationFilePermission = false
    ): bool
    {
        if (!\is_file($pathArchive)) {
            LoggerWrapper::error(
                'ZIP archive does NOT exist: ' . $pathArchive,
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
            );

            return false;
        }

        /** @noinspection ReturnFalseInspection */
        if (0 === \filesize($pathArchive)) {
            LoggerWrapper::error(
                'Cannot unzip archive with file size of 0: ' . $pathArchive,
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
            );

            return false;
        }

        if (!self::isExtensionInstalled()) {
            return false;
        }

        $zipArchive = new \ZipArchive();

        if (!$zipArchive->open($pathArchive)) {
            LoggerWrapper::error(
                'Failed opening ZIP archive: ' . $pathArchive,
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
            );

            return false;
        }

        if (!\is_dir($pathDestination)) {
            // Ensure destination path exists
            \mkdir($pathDestination, 0777, true);
        } elseif ($ensureDestinationFilePermission) {
            // Ensure destination path is writable
            \chmod($pathDestination, 0777);
        }

        $success = $zipArchive->extractTo($pathDestination);
        $zipArchive->close();

        if ($success) {
            return true;
        }

        LoggerWrapper::error(
            'Failed extracting ZIP archive: ' . $pathArchive . ' to path: ' . $pathDestination,
            [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
        );

        return false;
    }

    /**
     * @param string $pathArchive
     * @param string $filenameOrFilePath If in sub directory, prefix w/ path, e.g. "word/document.xml"
     * @param bool   $reduceToBasename   Optional: return content of 1st matching filename in any sub directory
     * @return string|false
     * @throws \Exception
     */
    public static function getContainedFileContents(
        string $pathArchive,
        string $filenameOrFilePath,
        bool $reduceToBasename = false
    )
    {
        if (!self::isExtensionInstalled()) {
            return false;
        }

        $zip = new \ZipArchive;

        if (!$zip->open($pathArchive)) {
            LoggerWrapper::error('Failed opening ZIP archive: ' . $pathArchive,
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
            );

            return false;
        }

        $content = false;

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $filenameAtIndex = $zip->getNameIndex($index);

            if ($reduceToBasename) {
                $filenameAtIndex = $filenameAtIndex === false
                    ? false
                    : \basename($filenameAtIndex);
            }

            if ($filenameAtIndex === $filenameOrFilePath) {
                $content = $zip->getFromIndex($index);
                break;
            }
        }

        $zip->close();

        return $content;
    }

    /**
     * @param string $filePath
     * @param   int  $errorCode
     * @param   int  $logLevel
     * @throws \Exception
     */
    private static function handleZipError(string $filePath, $errorCode, $logLevel = LOG_ERR): void
    {
        switch ($errorCode) {
            case self::ERROR_CODE_NO_FILES:
                $message = "Cannot zip: no files found in: $filePath";
                break;
            case self::ERROR_CODE_HAS_INVALID_FILES:
                $message = "Detected invalid files during zipping: $filePath";
                break;
            case ZipArchive::ER_EXISTS:
                $message = "Zip failed - Archive exists: $filePath";
                break;
            case ZipArchive::ER_INCONS:
                $message = "Un/zip failed - Archive is inconsistent: $filePath";
                break;
            case ZipArchive::ER_INVAL:
                $message = "Un/zip failed - Invalid argument: $filePath";
                break;
            case ZipArchive::ER_MEMORY:
                $message = "Un/Zip failed - Memory allocation error: $filePath";
                break;
            case ZipArchive::ER_NOENT:
                $message = "Un/zip failed - Cannot open file: $filePath";
                break;
            case ZipArchive::ER_READ:
                $message = "Un/zip failed - Read error: $filePath";
                break;
            case ZipArchive::ER_SEEK:
                $message = "Unzip failed - Positioning error: $filePath";
                break;
            default:
                $message = "Zip failed: $filePath";
                break;
        }

        LoggerWrapper::log(
            $message,
            (string)$logLevel,
            [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY, 'trace' => debug_backtrace()]
        );
    }
}
