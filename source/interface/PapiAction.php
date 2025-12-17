<?php

namespace Papi\interface;

use Papi\enumerator\HttpMethod;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

interface PapiAction
{
    public static function getMethod(): HttpMethod;

    public static function getPattern(): string;

    public function __invoke(Request $request, Response $response): Response;
}
