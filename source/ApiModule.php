<?php

namespace Papi;

abstract class ApiModule
{
    public array|null $prerequisite_list = null;

    public array|null $middleware_list = null;

    public array|null $definition_list = null;

    public array|null $action_list = null;

    public array|null $event_list = null;

    public function configure(): void
    {
        # ...
    }
}
