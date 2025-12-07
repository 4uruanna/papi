<?php

namespace Papi;

use Psr\Http\Server\MiddlewareInterface;

interface Middleware extends MiddlewareInterface
{
    public static function getPriority(): int;
}
