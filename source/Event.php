<?php

namespace Papi;

interface Event
{
    /**
     * @var int
     * @see \Papi\enumerator\EventPhases
     */
    public static function getPhase(): int;

    /**
     * @param mixed ...$args
     * @return void
     */
    public function __invoke(mixed ...$args): void;
}
