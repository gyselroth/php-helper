<?php

/**
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Tests\Fixtures;

class TestFilesHelper
{
    public const PATH_TMP       = __DIR__ . '/../tmp';
    public const PATH_TEMPLATES = __DIR__ . '/data/templates';

    /**
     * Ensure tmp directory for files created during tests exists and is empty
     *
     * @return bool
     */
    public static function emptyTmpDirectory(): bool
    {
        self::removeTmpDirectory();
        mkdir(self::PATH_TMP);

        return chmod(self::PATH_TMP, 0777);
    }

    public static function removeTmpDirectory(): void
    {
        if (file_exists(self::PATH_TMP)) {
            ini_set('display_errors', true);
            error_reporting(E_ALL);
            self::rmdirRecursive(self::PATH_TMP);
        }
    }

    /**
     * @param string $path
     */
    private static function rmdirRecursive($path): void
    {
        if (is_dir($path)) {
            $files = scandir($path, true);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $rmPath = $path . '/' . $file;
                    if (is_dir($rmPath)) {
                        self::rmdirRecursive($rmPath);
                    } else {
                        unlink($rmPath);
                    }
                }
            }
            reset($files);
            rmdir($path);
        }
    }

    /**
     * @return string
     */
    public static function getRandomTemplatePath(): string
    {
        $docxFiles = scandir(self::PATH_TEMPLATES, null);
        unset($docxFiles[1], $docxFiles[0]);
        $docxFiles = array_values($docxFiles);

        $amountTemplates  = \count($docxFiles);
        $templateFilename = $docxFiles[mt_rand(0, $amountTemplates - 1)];

        return self::PATH_TEMPLATES . '/' . $templateFilename;
    }
}
