<?php

namespace Papi\Test\mock;

use Papi\interface\PapiMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

class BarMiddleware implements PapiMiddleware
{
    public static int $registered_at;

    public static function register(App $app, array &$middlewares_map): bool
    {
        if (isset($middlewares_map[FooMiddleware::class])) {
            return true;
        }

        return false;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        self::$registered_at = time();
        sleep(1);
        return $handler->handle($request);
    }
}
