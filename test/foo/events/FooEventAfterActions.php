<?php

namespace Papi\Test\foo\events;

use Papi\enumerator\EventPhases;
use Papi\Event;
use Slim\App;

class FooEventAfterActions implements Event
{
    private static App $app;

    public static function getPhase(): string
    {
        return EventPhases::AFTER_ACTIONS;
    }

    public function __invoke(mixed ...$args): void
    {
        FooEventAfterActions::$app = $args[0];
    }
}
