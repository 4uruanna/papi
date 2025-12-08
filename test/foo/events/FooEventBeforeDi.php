<?php

namespace Papi\Test\foo\events;

use DI\ContainerBuilder;
use Papi\enumerator\EventPhases;
use Papi\Event;

class FooEventBeforeDi implements Event
{
    private static ContainerBuilder $builder;

    public static function getPhase(): string
    {
        return EventPhases::BEFORE_BUILD_DI;
    }

    public function __invoke(mixed ...$args): void
    {
        FooEventBeforeDi::$builder = $args[0];
    }
}
