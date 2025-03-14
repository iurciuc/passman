<?php

declare(strict_types=1);

namespace App\Application\Components\EventListener;

use App\Application\Components\Event\UserLoginEvent;

class UserLoginEventListener
{
    public function __invoke(UserLoginEvent $event): void
    {
        echo 'Hello!' . PHP_EOL;
    }
}
