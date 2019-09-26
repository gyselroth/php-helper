<?php

/**
 * Encryption helper classes
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Gyselroth\Helper;

use Gyselroth\Helper\Exception\ArgumentMissingException;
use Gyselroth\Helper\Exception\OperationFailedException;

/**
 * Application helpers
 */
class HelperCrypt
{
    // Triple DES open SSL encryption cipher
    public const OPEN_SSL_CIPHER = 'DES-EDE3-CBC';

    /**
     * Map base64 encoded token
     *
     * @param  string $token
     * @param  bool   $compressed
     * @return array|null|Object Decrypted data
     * @throws \Exception
     */
    public static function mapToken(string $token, bool $compressed = true)
    {
        $string = HelperString::urlSafeB64Decode($token);
        $string = $compressed
            ? \gzinflate($string)
            : $string;

        return HelperJson::decode($string);
    }

    /**
     * @param  string $string
     * @param  string $key
     * @param  boolean   $encodeUrlSafeBase64
     * @return string
     * @throws \Gyselroth\Helper\Exception\OperationFailedException
     * @throws \Gyselroth\Helper\Exception\ArgumentMissingException
     */
    public static function encrypt(string $string, string $key, bool $encodeUrlSafeBase64 = true): string
    {
        if (empty($string)) {
            return '';
        }

        if (empty($key)) {
            throw new ArgumentMissingException('Empty crypt key not allowed');
        }
        /** @noinspection CryptographicallySecureRandomnessInspection */
        $initVector = \openssl_random_pseudo_bytes(
            \openssl_cipher_iv_length(self::OPEN_SSL_CIPHER),
            $isStrong);

        if (false === $isStrong
            || false === $initVector
        ) {
            throw new OperationFailedException('IV generation for encryption failed');
        }

        $encrypted = $initVector . \openssl_encrypt(
            $string,
            self::OPEN_SSL_CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $initVector);

        return $encodeUrlSafeBase64
            ? HelperString::urlSafeB64encode($encrypted)
            : $encrypted;
    }

    /**
     * @param  string $encrypted
     * @param  string $password
     * @param  bool   $base64
     * @return string
     */
    public static function decrypt(string $encrypted, string $password, bool $base64 = true): string
    {
        if (empty($encrypted)) {
            return '';
        }

        $encrypted  = $base64
            ? HelperString::urlSafeB64Decode($encrypted)
            : $encrypted;

        $ivLength   = \openssl_cipher_iv_length(self::OPEN_SSL_CIPHER);
        $initVector = \substr($encrypted, 0, $ivLength);
        $encrypted  = \substr($encrypted, $ivLength);

        return \openssl_decrypt(
            $encrypted,
            self::OPEN_SSL_CIPHER,
            $password,
            OPENSSL_RAW_DATA,
            $initVector);
    }

    /**
     * Create base64 encoded token
     *
     * @param  array|Object|Zend_Json_Expr|string $data
     * @param  bool                               $compressed
     * @return string encrypted data
     */
    public static function createToken($data, $compressed = true): string
    {
        $string = $compressed
            ? \gzdeflate(\Zend_Json::encode($data), 9)
            : \Zend_Json::encode($data);

        return HelperString::urlSafeB64encode($string);
    }
}
