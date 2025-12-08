<?php

namespace Papi\Test\foo\middlewares;

use Papi\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class FooMiddlewareAfter implements Middleware
{
    public static int $execute_at;

    public static function getPriority(): int
    {
        return 999;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        self::$execute_at = time();
        return $handler->handle($request);
    }
}
