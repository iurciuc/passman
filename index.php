<?php

declare(strict_types=1);

use App\Application;
use App\Application\Router;
use App\Infrastructure\Adapter\FilesystemPasswordRepository;
use App\Infrastructure\Encryptor\SodiumEncryptor;
use App\Infrastructure\Filesystem\EncryptedFilesystem;
use App\Infrastructure\Filesystem\LocalFilesystem;

require_once __DIR__ . '/autoloader.php';

// not null
$password = readline('Enter master password: ');

exit((new Application(new FilesystemPasswordRepository(
    new EncryptedFilesystem(new LocalFilesystem(), new SodiumEncryptor($password)), __DIR__ . '/passwords'), new Router()))->run());
