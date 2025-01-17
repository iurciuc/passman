<?php

declare(strict_types=1);

namespace App\Application\Action;

readonly class CloseApplicationAction implements InvokableActionInterface
{
    public function __invoke(): void
    {
        echo 'Goodbye';
        exit();
    }
}
