<?php

declare(strict_types=1);

namespace App\Infrastructure;

interface FilesystemInterface
{
    public function read(string $path): string;

    public function write(string $path, string $contents): void;

    public function exists(string $path): bool;
}
