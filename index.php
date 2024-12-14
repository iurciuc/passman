<?php

declare(strict_types=1);

use App\Application;

require_once __DIR__ . '/autoloader.php';

exit((new Application())->run());
