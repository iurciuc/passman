<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Infrastructure\Adapter\FilesystemPasswordRepository;
use DomainException;

readonly class SearchByLoginAction implements InvokableActionInterface
{
    public function __construct(
        private FilesystemPasswordRepository $passwordsFileService
    ) {}

    public function __invoke(): void
    {
        $login = strtolower(readline('Enter login: '));

        try {
            $record = $this->passwordsFileService->find($login);
        } catch (DomainException $e) {
            echo $e->getMessage().PHP_EOL;

            return;
        }

        echo 'Password: '.$record['password'].PHP_EOL;
    }
}
