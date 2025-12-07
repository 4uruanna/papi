<?php

namespace Papi\Test\foo\events;

use Papi\enumerator\EventPhases;
use Papi\Event;

class FooEvent implements Event
{
    public static bool $loaded = false;


    public static function getPhase(): int
    {
        return EventPhases::BEFORE;
    }

    public function __invoke(mixed ...$args): void
    {
        FooEvent::$loaded = true;
    }
}
