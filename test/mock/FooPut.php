<?php

namespace Papi\Test\mock;

use Papi\abstract\PapiPut;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class FooPut extends PapiPut
{
    public static function getPattern(): string
    {
        return '/';
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $response
            ->withStatus(200)
            ->getBody()
            ->write("foo");
        return $response;
    }
}
