<?php

declare(strict_types=1);

namespace App;

use App\Actions\AddNewRecordAction;
use App\Actions\CloseApplicationAction;
use App\Actions\PrintAllLoginsAction;
use App\Actions\RemoveRecordAction;
use App\Actions\SearchByLoginAction;
use App\Enum\CommandListInterface;
use App\Enum\CommonCommands;
use App\Enum\PasswordCrudCommand;

class Router
{
    public function getRouteForCommand(CommandListInterface $command): string
    {
        return match ($command->getLabel()) {
            CommonCommands::EXIT->getLabel() => CloseApplicationAction::class,
            PasswordCrudCommand::SEARCH->getLabel() => SearchByLoginAction::class,
            PasswordCrudCommand::NEW->getLabel() => AddNewRecordAction::class,
            PasswordCrudCommand::REMOVE->getLabel() => RemoveRecordAction::class,
            PasswordCrudCommand::LIST->getLabel() => PrintAllLoginsAction::class,
        };
    }
}
