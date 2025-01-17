<?php

declare(strict_types=1);

namespace App\Infrastructure\Filesystem;

use App\Infrastructure\EncryptorInterface;
use App\Infrastructure\FilesystemInterface;

readonly class EncryptedFilesystem implements FilesystemInterface
{
    public function __construct(
        private FilesystemInterface $filesystem,
        private EncryptorInterface $encryptor,
    )
    {}

    public function read(string $path): string
    {
        return $this->encryptor->decrypt($this->filesystem->read($path));
    }

    public function write(string $path, string $contents): void
    {
        $this->filesystem->write($path, $this->encryptor->encrypt($contents));
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }
}
