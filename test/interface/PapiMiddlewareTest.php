<?php

namespace Papi\Test\interface;

use Papi\enumerator\HttpMethod;
use Papi\interface\PapiMiddleware;
use Papi\PapiBuilder;
use Papi\Test\mock\BarMiddleware;
use Papi\Test\mock\FooGet;
use Papi\Test\mock\FooMiddleware;
use Papi\Test\PapiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Slim\Psr7\Request;

#[CoversClass(PapiMiddleware::class)]
#[Medium]
final class PapiMiddlewareTest extends PapiTestCase
{
    private PapiBuilder $builder;
    private Request $request;

    public function setUp(): void
    {
        $this->request = $this->createRequest(HttpMethod::GET, "/");
        $this->builder = new PapiBuilder();
        $this->builder->addAction(FooGet::class);
    }

    public function testExecutionPriority(): void
    {
        $response = $this->builder
            ->addMiddleware(
                BarMiddleware::class,
                FooMiddleware::class
            )
            ->build()
            ->handle($this->request);

        $this->assertLessThan(
            FooMiddleware::$registered_at,
            BarMiddleware::$registered_at
        );
    }
}
