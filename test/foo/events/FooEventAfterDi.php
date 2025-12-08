<?php

namespace Papi\Test\foo\events;

use DI\Container;
use Papi\enumerator\EventPhases;
use Papi\Event;

class FooEventAfterDi implements Event
{
    private static Container $container;

    public static function getPhase(): string
    {
        return EventPhases::AFTER_BUILD_DI;
    }

    public function __invoke(mixed ...$args): void
    {
        self::$container = $args[0];
    }
}
