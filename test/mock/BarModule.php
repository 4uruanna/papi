<?php

namespace Papi\Test\mock;

use Papi\PapiModule;

use function DI\create;

class BarModule extends PapiModule
{
    public static function getPrerequisites(): array
    {
        return [FooModule::class];
    }

    public static function getDefinitions(): array
    {
        return [BarDefinition::class => create()->constructor()];
    }
}
