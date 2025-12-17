<?php

namespace Papi\Test\mock;

use Papi\enumerator\EventType;
use Papi\interface\PapiEventListener;

class AllEvent implements PapiEventListener
{
    public array $args_map = [];

    public function __invoke(EventType $event_type, array $options): void
    {
        $this->args_map[$event_type->value] = $options;
    }
}
