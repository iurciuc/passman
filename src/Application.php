<?php

declare(strict_types=1);

namespace App;

use App\Application\Command\CommandListInterface;
use App\Application\Command\CommonCommands;
use App\Application\Command\PasswordCrudCommand;
use App\Application\Router;
use App\Infrastructure\Adapter\FilesystemPasswordRepository;
use Throwable;

readonly class Application
{
    public function __construct(
        private FilesystemPasswordRepository $passwordsFileService,
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

        if (!$this->isMasterPasswordOk()) {
            die('Access denied!');
        }
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
            $this->passwordsFileService->findAll();
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
