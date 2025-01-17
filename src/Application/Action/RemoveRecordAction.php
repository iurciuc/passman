<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Infrastructure\Adapter\FilesystemPasswordRepository;

readonly class RemoveRecordAction implements InvokableActionInterface
{
    public function __construct(
        private FilesystemPasswordRepository $passwordsFileService
    ) {}

    public function __invoke(): void
    {
        $login = strtolower(readline('Enter login: '));

        if ($this->passwordsFileService->remove($login)) {
            echo 'Password removed'.PHP_EOL;
        } else {
            echo 'Login not found'.PHP_EOL;
        }
    }
}
