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

/**
 * Image helper methods
 */
class HelperImage
{
    public const LOG_CATEGORY = 'imaging';

    public static function saveThumbnail(
        string $sourcePath,
        string $thumbnailFile,
        int $maxWidth,
        int $maxHeight,
        int $quality = 100
    ): bool
    {
        $extension = \pathinfo($sourcePath)['extension'];

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

        $sourceWidth  = \imagesx($imageResource);
        $sourceHeight = \imagesy($imageResource);

        if (false === $sourceHeight || false === $sourceWidth) {
            return false;
        }

        $save = (($maxWidth / $maxHeight) < ($sourceWidth / $sourceHeight))
            ? \imagecreatetruecolor(
                $sourceWidth / ($sourceWidth / $maxWidth),
                $sourceHeight / ($sourceWidth / $maxWidth)
            )
            : \imagecreatetruecolor(
                $sourceWidth / ($sourceHeight / $maxHeight),
                $sourceHeight / ($sourceHeight / $maxHeight)
            );

        \imagecopyresampled(
            $save,
            $imageResource,
            0, 0, 0, 0,
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
     * @param  string $pathImage
     * @param  bool   $getImgTag Get img tag or just the image data?
     * @param  string $alt
     * @return string            Base64 encoded data of given image file
     */
    public static function encodeBase64(string $pathImage, bool $getImgTag = false, string $alt = ''): string
    {
        if (!file_exists($pathImage)) {
            // @todo add logging
            return '';
        }

        $type = \pathinfo($pathImage, PATHINFO_EXTENSION);

        $imageContents = \file_get_contents($pathImage);

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
            // @todo add logging
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
    public static function cropImage($imageFilename, $widthSubtrahend = 0, $heightSubtrahend = 0): bool
    {
        if (!\file_exists($imageFilename)) {
            LoggerWrapper::error(
                "Tried to crop non-existing image file: $imageFilename",
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]
            );

            return false;
        }

        $size       = \getimagesize($imageFilename);
        $cropWidth  = $size[0] - $widthSubtrahend;
        $cropHeight = $size[1] - $heightSubtrahend;

        $imageCopy  = \imagecreatefrompng($imageFilename);
        $imageNew   = \imagecreatetruecolor($cropWidth, $cropHeight);

        if (false === $imageNew) {
            // @todo add logging
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
    ): string
    {
        $pathTmpWithoutExtension = APPLICATION_PATH . '/../../tmp/' . \uniqid('img_', false);
        $pathTmpImage            = $pathTmpWithoutExtension . '.' . $extension;
        $pathScaledImage         = $pathTmpWithoutExtension . '_scaled.' . $extension;

        $fileHandle = \fopen($pathTmpImage, 'wb+');

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
     * @param string $extension
     * @return false|resource
     */
    public static function imageCreateByFormat(string $sourcePath, string $extension)
    {
        switch ($extension) {
            case HelperFile::FILE_ENDING_BMP:
                return \imagecreatefrombmp($sourcePath);
            case HelperFile::FILE_ENDING_GIF:
                return \imagecreatefromgif($sourcePath);
            case HelperFile::FILE_ENDING_PNG:
                return \imagecreatefrompng($sourcePath);
            default:
                return \imagecreatefromjpeg($sourcePath);
        }
    }
}
