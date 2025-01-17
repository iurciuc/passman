<?php

declare(strict_types=1);

namespace App\Infrastructure\Filesystem;

use App\Infrastructure\FilesystemInterface;

readonly class LocalFilesystem implements FilesystemInterface
{
    public function read(string $path): string
    {
        return file_get_contents($path);
    }

    public function write(string $path, string $contents): void
    {
        file_put_contents($path, $contents);
    }

    public function exists(string $path): bool
    {
        return file_exists($path);
    }
}
