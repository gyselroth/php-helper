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

class HelperMongoDB
{
    /**
     * Check if filename already exists
     *
     * @param  string $fileName
     * @return bool
     * @throws \Exception
     */
    public static function fileExists(string $fileName): bool
    {
        return (bool)(new MongoDb_Contents())
            ->getFile($fileName);
    }

    /**
     * Store content of given file into mongoDB
     *
     * @param  string $fileName    Name of the file
     * @param  string $tmpFileName Temp path of the file
     * @param  bool   $doReplaceSpaceByUnderscore
     * @return bool
     * @throws \Exception
     */
    public static function storeFileFromPath(
        string $fileName,
        string $tmpFileName,
        bool $doReplaceSpaceByUnderscore = true
    ): bool
    {
        if ($doReplaceSpaceByUnderscore) {
            $fileName = \str_replace(' ', '_', $fileName);
        }

        return (bool)(new MongoDb_Contents())
            ->storeFile($fileName, \file_get_contents($tmpFileName));
    }

    /**
     * @param  string $fileName
     * @param  bool   $doReplaceSpaceByUnderscore
     * @return bool
     * @throws \Exception
     */
    public static function deleteFile(string $fileName, bool $doReplaceSpaceByUnderscore = true): bool
    {
        if ($doReplaceSpaceByUnderscore) {
            $fileName = \str_replace(' ', '_', $fileName);
        }

        return (bool)(new MongoDb_Contents())
            ->removeFile($fileName);
    }

    /**
     * Check if filename already exists
     *
     * @param  string $fileName
     * @return bool
     * @throws \Exception
     */
    public static function doesFileExist(string $fileName): bool
    {
        return (bool)(new MongoDb_Contents())
            ->getFile($fileName);
    }

    /**
     * Put file into mongo collection
     *
     * @param   string $fileName
     * @param   string $tmpFileName
     * @return  bool
     * @throws \Exception
     */
    public static function createFile(string $fileName, string $tmpFileName): bool
    {
        return (bool)(new MongoDb_Contents())
            ->storeFile($fileName, \file_get_contents($tmpFileName));
    }
}
