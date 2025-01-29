<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Input;
use App\Application\Output;

interface InvokableActionInterface
{
    public function __invoke(Input $input, Output $output): void;
}
