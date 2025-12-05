<?php

namespace Papi;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

abstract class Action
{
    /**
     * @var string
     * @see \Papi\enumerator\HttpMethods
     */
    abstract public string $http_method { get; }

    /**
     * @var string
     */
    abstract public string $pattern { get; }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    abstract public function __invoke(Request $request, Response $response): Response;
}
