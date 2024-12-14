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

class Application
{
    private const string PASSWORDS_FILENAME = 'passwords';

    private ?string $password;

    public function __construct(
        private readonly PasswordsFileService $passwordsFileService
    ) {
        $this->password = null;
    }

    public function run(): bool
    {
        $this->loginMenu();

        while (true) {
            if (!$this->crudMenu()){
                $this->closeApplication();
            }
        }
    }

    private function loginMenu(): void
    {
        system('clear');

        if (!$this->passwordsFileService->fileExists()) {
            echo 'There is no passwords storage, creating a new one'.PHP_EOL;

            while(empty($this->password = readline('Enter a new master password: '))) {
                echo 'Password should be empty!' . PHP_EOL;
            }
        } else {
            do {
                $this->password = readline('Enter master password: ');
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

        $callback = $this->resolve_action($answer);
        $callback();

        readline('Press any key to continue...');

        system('clear');

        return true;
    }

    private function resolve_action(CommandListInterface $action): callable
    {
        return match ($action) {
            CommonCommands::EXIT => $this->closeApplication(...),
            PasswordCrudCommand::SEARCH => $this->searchPasswordByLogin(...),
            PasswordCrudCommand::NEW => $this->addNewPasswordAndLogin(...),
            PasswordCrudCommand::REMOVE => $this->removePasswordByLogin(...),
            PasswordCrudCommand::LIST => $this->printAllLogins(...),
        };
    }

    private function searchPasswordByLogin(): void
    {
        $fileData = $this->passwordsFileService->readPasswordsFile($this->password);

        $login = strtolower(readline('Enter login: '));

        if (array_key_exists($login, $fileData)) {
            echo 'Password: '.$fileData[$login].PHP_EOL;
        } else {
            echo 'Login not found'.PHP_EOL;
        }
    }

    private function addNewPasswordAndLogin(): void
    {
        $fileData = $this->passwordsFileService->readPasswordsFile($this->password);

        $login = strtolower(readline('Enter login: '));
        $password = readline('Enter password: ');

        $fileData[$login] = $password;

        echo 'Password added'.PHP_EOL;

        $this->passwordsFileService->saveDataToFile($fileData, $this->password);
    }

    private function removePasswordByLogin(): void
    {
        $fileData = $this->passwordsFileService->readPasswordsFile($this->password);

        $login = strtolower(readline('Enter login: '));

        if (array_key_exists($login, $fileData)) {
            unset($fileData[$login]);
            echo 'Password removed'.PHP_EOL;
        } else {
            echo 'Login not found'.PHP_EOL;
        }

        $this->passwordsFileService->saveDataToFile($fileData, $this->password);
    }

    private function printAllLogins(): void
    {
        $fileData = $this->passwordsFileService->readPasswordsFile($this->password);

        foreach ($fileData as $login => $password) {
            echo $login.PHP_EOL;
        }
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

    private function closeApplication(): void
    {
        echo 'Goodbye';
        exit();
    }

    private function isMasterPasswordOk(): bool
    {
        try {
            $this->passwordsFileService->readPasswordsFile($this->password);
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
