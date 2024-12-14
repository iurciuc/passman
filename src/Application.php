<?php

declare(strict_types=1);

namespace App;

use App\Enum\CommandListInterface;
use App\Enum\CommonCommands;
use App\Enum\PasswordCrudCommand;
use App\Service\FileEncryptorService;
use App\Service\PasswordsFileService;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use RangeException;
use Throwable;

readonly class Application
{
    public function __construct(
        private PasswordsFileService $passwordsFileService,
        private Router $router,
    ) {
    }

    public function run(): bool
    {
        $this->loginMenu();

        while (true) {
            $this->crudMenu();
        }
    }

    private function loginMenu(): void
    {
        system('clear');

        if (!$this->passwordsFileService->fileExists()) {
            echo 'There is no passwords storage, creating a new one'.PHP_EOL;

            while(empty($password = readline('Enter a new master password: '))) {
                echo 'Password should be empty!' . PHP_EOL;
            }

            $this->passwordsFileService->setPassword($password);
        } else {
            do {
                $this->passwordsFileService->setPassword(readline('Enter master password: '));
            } while (!$this->isMasterPasswordOk() && print('Master password is incorrect'.PHP_EOL));
        }

        system('clear');
    }

    private function crudMenu(): bool
    {
        $answer = $this->menu(
            'You are logged in',
            PasswordCrudCommand::cases()
        );

        system('clear');

        $action = $this->router->getRouteForCommand($answer);
        (new $action($this->passwordsFileService))();

        readline('Press any key to continue...');

        system('clear');

        return true;
    }

    /**
     * @param string $title
     * @param CommandListInterface[] $cases
     *
     * @return CommandListInterface
     */
    private function menu(string $title, array $cases): CommandListInterface
    {
        $cases[] = CommonCommands::EXIT;

        echo $title . PHP_EOL;

        foreach ($cases as $case) {
            echo '[' . $case->value . '] ' . $case->getLabel() . PHP_EOL;
        }

        while ($option = readline('Choose your option: ')) {
            foreach ($cases as $case) {
                if ($case->value === $option) {
                    return $case;
                }
            }

            echo 'Invalid option' . PHP_EOL;
        }

        return CommonCommands::EXIT;
    }


    private function isMasterPasswordOk(): bool
    {
        try {
            $this->passwordsFileService->readPasswordsFile();
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
