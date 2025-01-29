<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Input;
use App\Application\Output;
use App\Infrastructure\Adapter\FilesystemPasswordRepository;
use DomainException;

readonly class SearchByLoginAction implements InvokableActionInterface
{
    public function __construct(
        private FilesystemPasswordRepository $passwordsFileService
    ) {}

    public function __invoke(Input $input, Output $output): void
    {
        $login = strtolower($input->read('Enter login: '));

        try {
            $record = $this->passwordsFileService->find($login);
        } catch (DomainException $e) {
            $output->addLine($e->getMessage());

            return;
        }

        $output->addLine('Password: '.$record['password']);
    }
}
