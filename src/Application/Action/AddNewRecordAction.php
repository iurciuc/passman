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
        $login = strtolower(readline('Enter login: '));
        $password = readline('Enter password: ');

        $this->passwordsFileService->create($login, $password);

        echo 'Password added'.PHP_EOL;
    }
}
