<?php

namespace Papi\Test\abstract;

use Papi\abstract\PapiGet;
use Papi\enumerator\HttpMethod;
use Papi\PapiBuilder;
use Papi\Test\mock\FooGet;
use Papi\Test\PapiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PapiGet::class)]
final class PapiGetTest extends PapiTestCase
{
    public function test(): void
    {
        $request = $this->createRequest(HttpMethod::GET, "/");
        $builder = new PapiBuilder();
        $app = $builder->addAction(FooGet::class)->build();
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
