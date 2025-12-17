<?php

namespace Papi\Test\mock;

use Papi\enumerator\EventType;
use Papi\interface\PapiEventListener;

class FooEvent implements PapiEventListener
{
    public static bool $started = false;

    public function __invoke(EventType $event_type, array $options): void
    {
        if ($event_type === EventType::START) {
            self::$started = true;
        }
    }
}
