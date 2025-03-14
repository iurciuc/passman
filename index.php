<?php

declare(strict_types=1);

use App\Application;
use App\Application\Components\Event\UserLoginEvent;
use App\Application\Components\EventDispatcher\EventDispatcher;
use App\Application\Components\EventDispatcher\ListenerProvider;
use App\Application\Components\EventListener\UserLoginEventListener;
use App\Application\Router;
use App\Infrastructure\Adapter\FilesystemPasswordRepository;
use App\Infrastructure\Encryptor\SodiumEncryptor;
use App\Infrastructure\Filesystem\EncryptedFilesystem;
use App\Infrastructure\Filesystem\LocalFilesystem;

require_once __DIR__ . '/vendor/autoload.php';

// not null
$password = readline('Enter master password: ');

$listenerProvider = (new ListenerProvider())->addListener(UserLoginEvent::class, new UserLoginEventListener());

$dispatcher = new EventDispatcher($listenerProvider);
$dispatcher->dispatch(new UserLoginEvent());

sleep(1);

(new Application(new FilesystemPasswordRepository(
    new EncryptedFilesystem(new LocalFilesystem(), new SodiumEncryptor($password)), __DIR__ . '/passwords'), new Router()))->run();

exit();
