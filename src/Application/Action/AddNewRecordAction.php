<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Input;
use App\Application\Output;
use App\Infrastructure\Adapter\FilesystemPasswordRepository;

readonly class AddNewRecordAction implements InvokableActionInterface
{
    public function __construct(
        private FilesystemPasswordRepository $passwordsFileService
    ) {}

    public function __invoke(Input $input, Output $output): void
    {
        $login = strtolower($input->read('Enter login: '));
        $password = $input->read('Enter password: ');

        $this->passwordsFileService->create($login, $password);

        $output->addLine('Password added');
    }
}
