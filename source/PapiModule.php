<?php

namespace Papi;

abstract class PapiModule
{
    public static function getPrerequisites(): array
    {
        return [];
    }

    public static function getDefinitions(): array
    {
        return [];
    }

    public static function getEvents(): array
    {
        return [];
    }

    public static function getActions(): array
    {
        return [];
    }

    public static function getMiddlewares(): array
    {
        return [];
    }

    public static function configure(): void
    {
        // pass
    }
}
