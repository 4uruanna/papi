<?php

namespace Papi\Test\foo\events;

use Papi\enumerator\EventPhases;
use Papi\Event;

class FooEventBeforeBuild implements Event
{
    private static bool $loaded = false;

    public static function getPhase(): string
    {
        return EventPhases::BEFORE_BUILD;
    }

    public function __invoke(mixed ...$args): void
    {
        FooEventBeforeBuild::$loaded = true;
    }
}
