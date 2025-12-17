<?php

namespace Papi\Test\abstract;

use Papi\abstract\PapiPut;
use Papi\enumerator\HttpMethod;
use Papi\PapiBuilder;
use Papi\Test\mock\FooPut;
use Papi\Test\PapiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PapiPut::class)]
final class PapiPutTest extends PapiTestCase
{
    public function test(): void
    {
        $request = $this->createRequest(HttpMethod::PUT, "/");
        $builder = new PapiBuilder();
        $app = $builder->addAction(FooPut::class)->build();
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
