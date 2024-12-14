<?php

declare(strict_types=1);

namespace App\Actions;

readonly class CloseApplicationAction implements InvokableActionInterface
{
    public function __invoke(): void
    {
        echo 'Goodbye';
        exit();
    }
}
