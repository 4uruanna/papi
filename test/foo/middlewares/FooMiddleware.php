<?php

namespace Papi\Test\foo\middlewares;

use Papi\Middleware;
use Papi\Test\foo\services\FooService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class FooMiddleware implements Middleware
{
    public function __construct(FooService $fooService) {}

    public static function getPriority(): int
    {
        return 1;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
}
