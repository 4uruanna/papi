<?php

namespace Papi\interface;

use Papi\enumerator\EventType;

interface PapiEventListener
{
    public function __invoke(EventType $event_type, array $options): void;
}
