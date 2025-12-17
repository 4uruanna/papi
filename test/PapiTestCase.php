<?php

namespace Papi\Test;

use Papi\enumerator\HttpMethod;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Uri;

abstract class PapiTestCase extends TestCase
{
    protected function createRequest(
        HttpMethod $http_method,
        string $path,
        array $body = [],
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $server_parameters = [],
    ): Request {
        $uri = new Uri('', '', 80, $path);

        $handle = fopen('php://temp', 'w+');

        $stream_factory = new StreamFactory();

        $stream = $stream_factory->createStreamFromResource($handle);

        $headers_wrapper = new Headers();

        foreach ($headers as $name => $value) {
            $headers_wrapper->addHeader($name, $value);
        }

        $request = new Request(
            strtoupper($http_method->value),
            $uri,
            $headers_wrapper,
            $cookies,
            $server_parameters,
            $stream
        );

        $request = $request->withParsedBody($body);

        return $request;
    }
}
