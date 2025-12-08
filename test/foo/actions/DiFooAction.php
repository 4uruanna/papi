<?php

namespace Papi\Test\foo\actions;

use Papi\Action;
use Papi\enumerator\HttpMethods;
use Papi\Test\foo\services\FooService;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class DiFooAction implements Action
{
    private FooService $fooService;

    public function __construct(FooService $fooService)
    {
        $this->fooService = $fooService;
    }

    public static function getMethod(): string
    {
        return HttpMethods::GET;
    }

    public static function getPattern(): string
    {
        return '/f00';
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->getBody()
            ->write($this->fooService->getFoo());
        return $response;
    }
}
