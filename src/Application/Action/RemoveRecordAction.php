<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Input;
use App\Application\Output;
use App\Infrastructure\Adapter\FilesystemPasswordRepository;

readonly class RemoveRecordAction implements InvokableActionInterface
{
    public function __construct(
        private FilesystemPasswordRepository $passwordsFileService
    ) {}

    public function __invoke(Input $input, Output $output): void
    {
        $login = strtolower($input->read('Enter login: '));

        if ($this->passwordsFileService->remove($login)) {
            $output->addLine('Password removed');
        } else {
            $output->addLine('Login not found');
        }
    }
}
