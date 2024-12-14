<?php

declare(strict_types=1);

namespace App\Enum;

enum PasswordCrudCommand: string implements CommandListInterface
{
    case LIST = 'l';
    case SEARCH = 's';
    case NEW = 'n';
    case REMOVE = 'r';

    public function getLabel(): string
    {
        return match ($this) {
            self::LIST => 'List all logins',
            self::SEARCH => 'Search password by login',
            self::NEW => 'Add new password and login',
            self::REMOVE => 'Remove password by login',
        };
    }
}
