<?php

declare(strict_types=1);

namespace App\Infrastructure\Encryptor;

use App\Infrastructure\EncryptorInterface;
use Exception;
use RangeException;
use SensitiveParameter;

class SodiumEncryptor implements EncryptorInterface
{
    public function __construct(
        #[SensitiveParameter] private string $password,
    ) {}

    public function encrypt(string $data): string
    {
        $key = $this->computeKey();

        if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RangeException('Key is not the correct size (must be 32 bytes).');
        }

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $cipher = base64_encode(
            $nonce.
            sodium_crypto_secretbox(
                $data,
                $nonce,
                $key
            )
        );
        sodium_memzero($data);
        sodium_memzero($key);

        return $cipher;
    }

    public function decrypt(string $data): string
    {
        $key = $this->computeKey();

        $decoded = base64_decode($data);
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

    private function computeKey(): string
    {
        return sodium_crypto_pwhash(
            SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
            $this->password,
            '1234567890abcdef',
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
        );
    }
}
