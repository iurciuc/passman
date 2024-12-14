<?php

declare(strict_types=1);

namespace App\Actions;

use App\Service\PasswordsFileService;

readonly class SearchByLoginAction implements InvokableActionInterface
{
    public function __construct(
        private PasswordsFileService $passwordsFileService
    ) {}

    public function __invoke(): void
    {
        $fileData = $this->passwordsFileService->readPasswordsFile();

        $login = strtolower(readline('Enter login: '));

        if (array_key_exists($login, $fileData)) {
            echo 'Password: '.$fileData[$login].PHP_EOL;
        } else {
            echo 'Login not found'.PHP_EOL;
        }
    }
}
