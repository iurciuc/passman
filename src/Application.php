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
        // 0. Input + Output
        // 1. composer init
        // 2. composer require php-fig/event-dispatcher (PSR-14)
        // 3. Write an implementation of EventDipatcher in Component/EventDispatcherFolder
        // 4. Write a listener provider (an array of listener classes/functions that will handle the events)
        // -------------------
        // 5. Define kernel event (as objects)
        //  - [ ] requets
        //  - [ ] controller (ContollerEvent::setController(...)) # SIDENOTE: controller = action[], where `action` ~= `controller`
        //      - [ ] listener that will identify requested route and will set it on the event that was dispatched
        //  - [ ] exception -> when an exception is thrown and should be handled or application execution should be halted.
        //      - [ ] exception listener that will transform exception into a formated message using $output.
        //  - [ ] response
        //  - [ ] terminate -> when users tries to exit the application (ex: __destruct)
        //      - [ ] ex: listener that will persist changes in the storage
        //  Next Step: Container
        $this->loginMenu();

        while (true) {
            $this->crudMenu();
            // $output->flush(); // system('clear'), later $this->send($response);
        }
    }

    // TODO: Move this check to RequestEvent, very similar to AuthMiddleware
    private function loginMenu(): void
    {
        // try to move it to main lifecycle (method run), but use it from $output
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
