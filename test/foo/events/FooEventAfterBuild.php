<?php

namespace Papi\Test\foo\events;

use Papi\enumerator\EventPhases;
use Papi\Event;

class FooEventAfterBuild implements Event
{
    private static bool $loaded = false;

    public static function getPhase(): string
    {
        return EventPhases::AFTER_BUILD;
    }

    public function __invoke(mixed ...$args): void
    {
        FooEventAfterBuild::$loaded = true;
    }
}
