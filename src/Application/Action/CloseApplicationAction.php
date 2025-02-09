<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Input;
use App\Application\Output;

readonly class CloseApplicationAction implements InvokableActionInterface
{
    public function __invoke(Input $input, Output $output): void
    {
        $output->addLine('Goodbye!');
        $output->flush();

        exit();
    }
}
