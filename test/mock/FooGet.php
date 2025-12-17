<?php

namespace Papi\Test\mock;

use Papi\abstract\PapiGet;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class FooGet extends PapiGet
{
    public static function getPattern(): string
    {
        return '/';
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->getBody()
            ->write("foo");
        return $response;
    }
}
