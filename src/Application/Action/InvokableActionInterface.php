<?php

declare(strict_types=1);

namespace App\Application\Action;

interface InvokableActionInterface
{
    public function __invoke(): void;
}
