<?php

declare(strict_types=1);

namespace App\Actions;

use App\Service\PasswordsFileService;

readonly class RemoveRecordAction implements InvokableActionInterface
{
    public function __construct(
        private PasswordsFileService $passwordsFileService
    ) {}

    public function __invoke(): void
    {
        $fileData = $this->passwordsFileService->readPasswordsFile();

        $login = strtolower(readline('Enter login: '));

        if (array_key_exists($login, $fileData)) {
            unset($fileData[$login]);
            echo 'Password removed'.PHP_EOL;
        } else {
            echo 'Login not found'.PHP_EOL;
        }

        $this->passwordsFileService->saveDataToFile($fileData);
    }
}
