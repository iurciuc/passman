<?php

declare(strict_types=1);

namespace App\Service;

use Exception;
use RangeException;

class FileEncryptorService
{
    public function safeEncrypt(string $message, string $password): string
    {
        $key = $this->computeKey($password);

        if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RangeException('Key is not the correct size (must be 32 bytes).');
        }

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $cipher = base64_encode(
            $nonce.
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

    public function safeDecrypt(string $encrypted, string $password): string
    {
        $key = $this->computeKey($password);

        $decoded = base64_decode($encrypted);
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $plain = sodium_crypto_secretbox_open(
            $ciphertext,
            $nonce,
            $key
        );

        if (!is_string($plain)) {
            throw new Exception('Invalid MAC');
        }

        sodium_memzero($ciphertext);
        sodium_memzero($key);

        return $plain;
    }

    private function computeKey(string $password): string
    {
        return sodium_crypto_pwhash(
            SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
            $password,
            '1234567890abcdef',
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
        );
    }
}
