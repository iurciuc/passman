<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Infrastructure\Adapter\FilesystemPasswordRepository;

readonly class PrintAllLoginsAction implements InvokableActionInterface
{
    public function __construct(
        private FilesystemPasswordRepository $passwordsFileService
    ) {}

    public function __invoke(): void
    {
        $fileData = $this->passwordsFileService->findAll();

        foreach ($fileData as $login => $password) {
            echo $login.PHP_EOL;
        }
    }
}
