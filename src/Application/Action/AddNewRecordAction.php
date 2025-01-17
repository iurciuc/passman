<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Infrastructure\Adapter\FilesystemPasswordRepository;

readonly class AddNewRecordAction implements InvokableActionInterface
{
    public function __construct(
        private FilesystemPasswordRepository $passwordsFileService
    ) {}

    public function __invoke(): void
    {
        // TODO: Move all echo/print statements and readline calls to Input and Output classes
        // NOTE: Try to make output in one call. Ex: print, table, status(ok, bad request)
        $login = strtolower(readline('Enter login: '));
        $password = readline('Enter password: ');

        $this->passwordsFileService->create($login, $password);

        // EX: $output->ok('Password added'); return;
        echo 'Password added'.PHP_EOL;
    }
}
