<?php

namespace Papi;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

interface Action
{
    /**
     * @var string
     * @see \Papi\enumerator\HttpMethods
     */
    public static function getHttpMethod(): string;

    /**
     * @var string
     */
    public static function getPattern(): string;

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response;
}
