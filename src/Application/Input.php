<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Command\CommandListInterface;

class Input
{
    private ?CommandListInterface $command = null;

    public function setCommand(CommandListInterface $command): void
    {
        $this->command = $command;
    }

    public function getCommand(): ?CommandListInterface
    {
        return $this->command;
    }

    public function read(?string $prompt = null): string
    {
        return readline($prompt);
    }
}
