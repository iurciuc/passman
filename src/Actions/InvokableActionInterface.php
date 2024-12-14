<?php

declare(strict_types=1);

namespace App\Actions;

interface InvokableActionInterface
{
    public function __invoke(): void;
}
