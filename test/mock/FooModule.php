<?php

namespace Papi\Test\mock;

use Papi\PapiModule;

use function DI\create;

class FooModule extends PapiModule
{
    public static function getActions(): array
    {
        return [FooGet::class];
    }

    public static function getDefinitions(): array
    {
        return [FooDefinition::class => create()->constructor()];
    }

    public static function getEvents(): array
    {
        return [FooEvent::class];
    }

    public static function getMiddlewares(): array
    {
        return [FooMiddleware::class];
    }
}
