<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Action\AddNewRecordAction;
use App\Application\Action\CloseApplicationAction;
use App\Application\Action\PrintAllLoginsAction;
use App\Application\Action\RemoveRecordAction;
use App\Application\Action\SearchByLoginAction;
use App\Application\Command\CommandListInterface;
use App\Application\Command\CommonCommands;
use App\Application\Command\PasswordCrudCommand;

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
