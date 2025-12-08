<?php

namespace Papi\Test\foo\events;

use Papi\enumerator\EventPhases;
use Papi\Event;
use Slim\App;

class FooEventBeforeMiddlewares implements Event
{
    private static App $app;
    private static array $middleware_list;

    public static function getPhase(): string
    {
        return EventPhases::BEFORE_MIDDLEWARES;
    }

    public function __invoke(mixed ...$args): void
    {
        FooEventBeforeMiddlewares::$app = $args[0];
        FooEventBeforeMiddlewares::$middleware_list = $args[1];
    }
}
