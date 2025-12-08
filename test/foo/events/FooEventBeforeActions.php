<?php

namespace Papi\Test\foo\events;

use Papi\enumerator\EventPhases;
use Papi\Event;
use Slim\App;

class FooEventBeforeActions implements Event
{
    private static App $app;
    private static array $route_list;

    public static function getPhase(): string
    {
        return EventPhases::BEFORE_ACTIONS;
    }

    public function __invoke(mixed ...$args): void
    {
        FooEventBeforeActions::$app = $args[0];
        FooEventBeforeActions::$route_list = $args[1];
    }
}
