<?php

/**
 * Copyright (c) 2017-2020 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Gyselroth\Helper;

use Gyselroth\Helper\Exception\FileException;
use Gyselroth\HelperLog\LoggerWrapper;

/**
 * Image helper methods
 */
class HelperImage
{
    public const LOG_CATEGORY = 'imaging';

    /**
     * Detect whether given file extension of image file does not match it's MIME type,
     * rename resp. file when necessary
     *
     * @param string $imageFilePath
     * @param string $imageFileExtension
     * @return mixed|string|string[]
     * @throws FileException
     */
    public static function ensureCorrectImageFileExtension(string $imageFilePath, string &$imageFileExtension)
    {
        $extensionInFilename = \pathinfo($imageFilePath, PATHINFO_EXTENSION);

        $contentMime = \mime_content_type($imageFilePath);

        if (false === $contentMime) {
            throw new FileException("Failed to retrieve MIME type");
        }

        $contentMimeParts = \explode('/', $contentMime);

        if (\count($contentMimeParts) < 2) {
            throw new FileException("Failed to retrieve MIME type");
        }

        $extensionByMimeType = $contentMimeParts[1];

        if ($extensionByMimeType !== \strtolower($extensionInFilename)) {
            $filePathIncorrect = $imageFilePath;

            $imageFilePath = \str_replace(
                '.' . $extensionInFilename,
                '.' . $extensionByMimeType,
                $imageFilePath
            );

            if (!\rename($filePathIncorrect, $imageFilePath)) {
                throw new FileException("Failed to rename $filePathIncorrect to $imageFilePath");
            }

            $imageFileExtension = $extensionByMimeType;
        }

        return $imageFilePath;
    }

    public static function saveThumbnail(
        string $sourcePath,
        string $thumbnailFile,
        int $maxWidth,
        int $maxHeight,
        int $quality = 100
    ): bool {
        $extension = \strtolower(\pathinfo($sourcePath, PATHINFO_EXTENSION));

        $imageResource = '' === $sourcePath
            ? false
            : self::imageCreateByFormat($sourcePath, $extension);

        if (!$imageResource) {
            $imageResource = \imagecreate($maxWidth, $maxHeight);

            if (false === $imageResource) {
                return false;
            }

            $white = \imagecolorallocate($imageResource, 255, 255, 255);

            \imagefilledrectangle($imageResource, 0, 0, $maxWidth, $maxHeight, $white);
        }

        $sourceWidth = \imagesx($imageResource);

        if (0 === (int)$sourceWidth) {
            return false;
        }

        $sourceHeight = \imagesy($imageResource);

        $save = (($maxWidth / $maxHeight) < ($sourceWidth / $sourceHeight))
            ? \imagecreatetruecolor(
                (int)$sourceWidth / (int)($sourceWidth / $maxWidth),
                (int)$sourceHeight / (int)($sourceWidth / $maxWidth)
            )
            : \imagecreatetruecolor(
                (int)$sourceWidth / (int)($sourceHeight / $maxHeight),
                (int)$sourceHeight / (int)($sourceHeight / $maxHeight)
            );

        if (!$save) {
            return false;
        }

        \imagecopyresampled(
            $save,
            $imageResource,
            0,
            0,
            0,
            0,
            \imagesx($save),
            \imagesy($save),
            $sourceWidth,
            $sourceHeight
        );

        // Changes the compression and quality to 0, quality has to be between 0 and 9
        \imagepng(
            $save,
            $thumbnailFile,
            (int)($quality > 0 ? (100 - $quality) / 10 : 9)
        );

        \imagedestroy($imageResource);
        \imagedestroy($save);

        $file = \file($thumbnailFile);

        return $file ? !empty($file[0]) : false;
    }

    /**
     * @param string $pathImage
     * @param bool $getImgTag   Get img tag or just image data?
     * @param string $alt
     * @return string           Base64 encoded data of given image file
     * @throws \Exception
     */
    public static function encodeBase64(string $pathImage, bool $getImgTag = false, string $alt = ''): string
    {
        if (!\file_exists($pathImage)) {
            LoggerWrapper::warning(
                "HelperImage::encodeBase64 - File not found $pathImage",
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY, LoggerWrapper::OPT_PARAMS => $pathImage]
            );

            return '';
        }

        $type = \strtolower(\pathinfo($pathImage, PATHINFO_EXTENSION));

        $imageContents = \file_get_contents($pathImage);

        if (!$imageContents) {
            return '';
        }

        $encoded = 'data:image/' . $type . ';base64,' . \base64_encode($imageContents);

        if ($getImgTag) {
            [$width, $height] = \getimagesize($pathImage);

            return '<img src="' . $encoded . '" '
                . 'width="' . $width . '" '
                . 'height="' . $height . '"'
                . ('' === $alt ? '' : ' alt="' . $alt . '"')
                . ' />';
        }

        return $encoded;
    }

    public static function saveTransparentImage(int $width, int $height, string $filePath): bool
    {
        $image = \imagecreatetruecolor($width, $height);

        if (false === $image) {
            LoggerWrapper::warning(
                'HelperImage::saveTransparentImage - imagecreatetruecolor failed',
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
            );

            return false;
        }

        \imagesavealpha($image, true);

        $transparentColor = \imagecolorallocatealpha($image, 255, 255, 255, 127);
        \imagefill($image, 0, 0, $transparentColor);

        return \imagepng($image, $filePath);
    }

    /**
     * @param string $imageFilename
     * @param int $widthSubtrahend
     * @param int $heightSubtrahend
     * @return bool
     * @throws \Exception
     */
    public static function cropImage(
        string $imageFilename,
        int $widthSubtrahend = 0,
        int $heightSubtrahend = 0
    ): bool {
        if (!\file_exists($imageFilename)) {
            LoggerWrapper::error(
                "Tried to crop non-existing image file: $imageFilename",
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
            );

            return false;
        }

        $size = \getimagesize($imageFilename);

        if (!$size) {
            return false;
        }

        $cropWidth  = $size[0] - $widthSubtrahend;
        $cropHeight = $size[1] - $heightSubtrahend;

        $imageCopy  = \imagecreatefrompng($imageFilename);
        $imageNew   = \imagecreatetruecolor($cropWidth, $cropHeight);

        if (false === $imageNew || false === $imageCopy) {
            LoggerWrapper::warning(
                'HelperImage::saveTransparentImage - imagecreatetruecolor failed',
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
            );

            return false;
        }

        \imagesavealpha($imageNew, true);
        \imagecopy($imageNew, $imageCopy, 0, 0, 0, 0, $cropWidth, $cropHeight);

        return \imagepng($imageNew, $imageFilename);
    }

    /**
     * @param string $imageData
     * @param int $maxWidth
     * @param int $maxHeight
     * @param string $extension
     * @param int $quality
     * @return string
     * @throws FileException
     */
    public static function scaleImageByData(
        string $imageData,
        int $maxWidth,
        int $maxHeight,
        string $extension = HelperFile::FILE_ENDING_JPEG,
        int $quality = 100
    ): string {
        $applicationPath = \defined('APPLICATION_PATH')
            ? \constant('APPLICATION_PATH')
            : __DIR__ . '/../../../../../../src/application/';

        $pathTmpWithoutExtension = $applicationPath . '/../../tmp/' . \uniqid('img_', false);

        $pathTmpImage    = $pathTmpWithoutExtension . '.' . $extension;
        $pathScaledImage = $pathTmpWithoutExtension . '_scaled.' . $extension;

        $fileHandle = \fopen($pathTmpImage, 'wb+');

        if (!$fileHandle) {
            throw new FileException('Failed open image: ' . $pathTmpImage);
        }

        \fwrite($fileHandle, $imageData);
        \fclose($fileHandle);

        if (!self::saveThumbnail($pathTmpImage, $pathScaledImage, $maxWidth, $maxHeight, $quality)) {
            throw new FileException('Failed store scaled image: ' . $pathScaledImage);
        }

        $contents = \file_get_contents($pathScaledImage);

        if (false === $contents) {
            throw new FileException('Failed store scaled image: ' . $pathScaledImage);
        }

        \unlink($pathScaledImage);

        return $contents;
    }

    /**
     * @param string $sourcePath
     * @param string $mimeType
     * @return false|resource
     */
    public static function createImageResourceByMimeType(string $sourcePath, string $mimeType)
    {
        switch ($mimeType) {
            case HelperFile::FILE_ENDING_BMP:
                return \imagecreatefrombmp($sourcePath);
            case HelperFile::FILE_ENDING_GIF:
                return \imagecreatefromgif($sourcePath);
            case HelperFile::FILE_ENDING_PNG:
                return \imagecreatefrompng($sourcePath);
            case HelperFile::FILE_ENDING_JPEG:
                // no-break
            case HelperFile::FILE_ENDING_JPG:
            default:
                return \imagecreatefromjpeg($sourcePath);
        }
    }

    /**
     * Obtain image data from given filename and MIME, on failure: retry w/ correct MIME if detectable
     *
     * @param string $sourcePath
     * @param string $mimeType
     * @return false|resource
     * @throws \Exception
     */
    public static function imageCreateByFormat(string $sourcePath, string $mimeType)
    {
        $imageResource = self::createImageResourceByMimeType($sourcePath, $mimeType);

        if (false !== $imageResource) {
            return $imageResource;
        }

        $requestedMimeType = $mimeType;
        $contentMime = \mime_content_type($sourcePath);

        if (false !== $contentMime) {
            $contentMimeParts = \explode('/', $contentMime);

            if (count($contentMimeParts) > 1) {
                $mimeType = $contentMimeParts[1];

                return self::createImageResourceByMimeType($sourcePath, $mimeType);
            }
        }

        LoggerWrapper::warning(
            'HelperImage::imageCreateByFormat - Obtaining image data failed.'
            . "Requested MIME was: $requestedMimeType - retrying w/ detected MIME type: $mimeType",
            [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
        );

        return false;
    }
}
