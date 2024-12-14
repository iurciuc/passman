<?php

declare(strict_types=1);

namespace App\Actions;

use App\Service\PasswordsFileService;

readonly class AddNewRecordAction implements InvokableActionInterface
{
    public function __construct(
        private PasswordsFileService $passwordsFileService
    ) {}

    public function __invoke(): void
    {
        $fileData = $this->passwordsFileService->readPasswordsFile();

        $login = strtolower(readline('Enter login: '));
        $password = readline('Enter password: ');

        $fileData[$login] = $password;

        echo 'Password added'.PHP_EOL;

        $this->passwordsFileService->saveDataToFile($fileData);
    }
}
