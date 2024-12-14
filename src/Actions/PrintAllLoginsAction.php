<?php

declare(strict_types=1);

namespace App\Actions;

use App\Service\PasswordsFileService;

readonly class PrintAllLoginsAction implements InvokableActionInterface
{
    public function __construct(
        private PasswordsFileService $passwordsFileService
    ) {}

    public function __invoke(): void
    {
        $fileData = $this->passwordsFileService->readPasswordsFile();

        foreach ($fileData as $login => $password) {
            echo $login.PHP_EOL;
        }
    }
}
