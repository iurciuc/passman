<?php

declare(strict_types=1);

use App\Application;
use App\Service\FileEncryptorService;
use App\Service\PasswordsFileService;

require_once __DIR__ . '/autoloader.php';

exit((new Application(new PasswordsFileService(new FileEncryptorService())))->run());
