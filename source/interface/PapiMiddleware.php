<?php

namespace Papi\interface;

use Psr\Http\Server\MiddlewareInterface;
use Slim\App;

interface PapiMiddleware extends MiddlewareInterface
{
    public static function register(App $app, array &$middlewares_map): bool;
}
