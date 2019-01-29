<?php

/**
 * Class Encryption
 * @author Marek Mazur
 */
class Encryption
{
    /**
     * Encryption constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $message
     * @return null|string
     * @throws Exception
     */
    static public function encrypt($message, $key)
    {
        if (empty($message) || empty($key)) {
            return null;
        }

        $key = hex2bin($key);

        if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            return null;
        }

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $cipher = base64_encode(
            $nonce .
            sodium_crypto_secretbox(
                $message,
                $nonce,
                $key
            )
        );
        sodium_memzero($message);
        sodium_memzero($key);

        return $cipher;
    }

    /**
     * @param string $encrypted
     * @return null|string
     */
    static public function decrypt($encrypted, $key)
    {
        if (empty($encrypted) || empty($key)) {
            return null;
        }

        $key = hex2bin($key);

        $decoded = base64_decode($encrypted);
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $decrypted = sodium_crypto_secretbox_open(
            $ciphertext,
            $nonce,
            $key
        );
        if (!is_string($decrypted)) {
            return null;
        }

        sodium_memzero($ciphertext);
        sodium_memzero($key);

        return $decrypted;
    }

    /**
     * @return string
     * @throws Exception
     */
    static public function generateKey()
    {
        return bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES));
    }
}
