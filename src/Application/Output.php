<?php

declare(strict_types=1);

namespace App\Application;

class Output
{
    private string $output = '';

    public function addLine(string $string): void
    {
        $this->output .= $string . PHP_EOL;
    }

    public function flush(): void
    {
        echo $this->output;
    }

    public function clearScreen(): void
    {
        system('clear');
    }

    public function isEmpty(): bool
    {
        return empty($this->output);
    }
}
