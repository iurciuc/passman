<?php

declare(strict_types=1);

enum CommonCommands: string implements CommandListInterface
{
    case EXIT = 'e';

    public function getLabel(): string
    {
        return match ($this) {
            self::EXIT => 'Exit',
        };
    }
}
