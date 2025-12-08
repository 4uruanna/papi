<?php

namespace Papi\Test\foo\events;

use Papi\enumerator\EventPhases;
use Papi\Event;
use Slim\App;

class FooEventAfterMiddlewares implements Event
{
    private static App $app;

    public static function getPhase(): string
    {
        return EventPhases::AFTER_MIDDLEWARES;
    }

    public function __invoke(mixed ...$args): void
    {
        FooEventAfterMiddlewares::$app = $args[0];
    }
}
