<?php

namespace Papi\Test\foo\events;

use Papi\enumerator\EventPhases;
use Papi\Event;

class FooEvent extends Event
{
    public static bool $loaded = false;

    public int $phase = EventPhases::BEFORE;

    public function __invoke(mixed ...$args): void
    {
        FooEvent::$loaded = true;
    }
}
