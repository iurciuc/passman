<?php

declare(strict_types=1);

namespace App\Application\Components\Event;

use App\Application\Components\EventDispatcher\Event;
use Psr\EventDispatcher\StoppableEventInterface;

class UserLoginEvent extends Event
{
}
