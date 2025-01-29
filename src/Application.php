<?php

declare(strict_types=1);

namespace App;

use App\Application\Command\CommandListInterface;
use App\Application\Command\CommonCommands;
use App\Application\Command\PasswordCrudCommand;
use App\Application\Input;
use App\Application\Output;
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
        // 0. Input + Output
        // 1. composer init
        // 2. composer require php-fig/event-dispatcher (PSR-14)

        // 3. Write an implementation of EventDispatcher in Component/EventDispatcherFolder
        // 4. Write a listener provider (an array of listener classes/functions that will handle the events)
        // -------------------
        // 5. Define kernel event (as objects)
        //  - [ ] request
        //  - [ ] controller (ControllerEvent::setController(...)) # SIDENOTE: controller = action[], where `action` ~= `controller`
        //      - [ ] listener that will identify requested route and will set it on the event that was dispatched
        //  - [ ] exception -> when an exception is thrown and should be handled or application execution should be halted.
        //      - [ ] exception listener that will transform exception into a formated message using $output.
        //  - [ ] response
        //  - [ ] terminate -> when users tries to exit the application (ex: __destruct)
        //      - [ ] ex: listener that will persist changes in the storage
        //  Next Step: Container

        $input = new Input();
        $output = new Output();

        $output->clearScreen();

        if (!$this->isMasterPasswordOk()) {
            die('Access denied!');
        }

        while (true) {
            $this->crudMenu($input, $output);

            if ($output->isEmpty()) {
                break;
            }

            $output->flush();

            $input->read('Press any key to continue...');
            $output->clearScreen();
        }

        return true;
    }

    private function crudMenu(Input $input, Output $output): void
    {
        $command = $this->menu(
            'You are logged in',
            PasswordCrudCommand::cases()
        );

        $output->clearScreen();

        $input->setCommand($command);

        $action = $this->router->getActionForCommand($command);

        (new $action($this->passwordsFileService))($input, $output);
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
