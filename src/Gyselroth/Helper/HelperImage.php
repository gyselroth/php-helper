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
        $imageResource = '' === $sourcePath
            ? false
            : @imagecreatefromjpeg($sourcePath);

        if (!$imageResource) {
            $imageResource = \imagecreate($maxWidth, $maxHeight);
            $white         = \imagecolorallocate($imageResource, 255, 255, 255);
            \imagefilledrectangle($imageResource, 0, 0, $maxWidth, $maxHeight, $white);
        }

        $sourceWidth  = \imagesx($imageResource);
        $sourceHeight = \imagesy($imageResource);

        $save = (($maxWidth / $maxHeight) < ($sourceWidth / $sourceHeight))
            ? \imagecreatetruecolor(
                $sourceWidth / ($sourceWidth / $maxWidth),
                $sourceHeight / ($sourceWidth / $maxWidth))
            : \imagecreatetruecolor(
                $sourceWidth / ($sourceHeight / $maxHeight),
                $sourceHeight / ($sourceHeight / $maxHeight));

        \imagecopyresampled(
            $save,
            $imageResource,
            0, 0, 0, 0,
            \imagesx($save),
            \imagesy($save),
            $sourceWidth,
            $sourceHeight);

        // Changes the compression and quality to 0, quality has to be between 0 and 9
        \imagepng($save, $thumbnailFile, (int)($quality > 0 ? (100 - $quality) / 10 : 9));

        \imagedestroy($imageResource);
        \imagedestroy($save);

        $file = \file($thumbnailFile);

        return !empty($file[0]);
    }

    /**
     * @param  string $pathImage
     * @param  bool   $getImgTag Get img tag or just the image data?
     * @param  string $alt
     * @return string            Base64 encoded data of given image file
     */
    public static function encodeBase64(string $pathImage, bool $getImgTag = false, string $alt = ''): string
    {
        $type    = \pathinfo($pathImage, PATHINFO_EXTENSION);
        $encoded = 'data:image/' . $type . ';base64,' . \base64_encode(\file_get_contents($pathImage));

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
                [LoggerWrapper::OPT_CATEGORY => self::LOG_CATEGORY]);

            return false;
        }

        $size       = \getimagesize($imageFilename);
        $cropWidth  = $size[0] - $widthSubtrahend;
        $cropHeight = $size[1] - $heightSubtrahend;

        $imageCopy  = \imagecreatefrompng($imageFilename);
        $imageNew   = \imagecreatetruecolor($cropWidth, $cropHeight);

        \imagesavealpha($imageNew, true);
        \imagecopy($imageNew, $imageCopy, 0, 0, 0, 0, $cropWidth, $cropHeight);

        return \imagepng($imageNew, $imageFilename);
    }
}
