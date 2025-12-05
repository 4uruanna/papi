<?php

namespace Papi;

abstract class Event
{
    /**
     * @var int
     * @see \Papi\enumerator\EventPhases
     */
    abstract public int $phase { get; }

    /**
     * @param mixed ...$args
     * @return void
     */
    abstract public function __invoke(mixed ...$args): void;
}
