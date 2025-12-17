<?php

namespace Papi\Test\mock;

use Papi\interface\PapiMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

class FooMiddleware implements PapiMiddleware
{
    public static function register(App $app, array &$middlewares_map): bool
    {
        return true;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
}
