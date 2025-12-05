<?php

namespace Papi\Test\foo\actions;

use Papi\Action;
use Papi\enumerator\HttpMethods;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class FooAction extends Action
{
    public string $http_method = HttpMethods::GET;

    public string $pattern = '/';

    public function __invoke(Request $request, Response $response): Response
    {
        $response->getBody()->write(json_encode(["hello" => "world"]));
        return $response;
    }
}
