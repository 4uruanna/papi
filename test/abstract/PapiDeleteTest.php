<?php

namespace Papi\Test\abstract;

use Papi\abstract\PapiDelete;
use Papi\enumerator\HttpMethod;
use Papi\PapiBuilder;
use Papi\Test\mock\FooDelete;
use Papi\Test\PapiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PapiDelete::class)]
final class PapiDeleteTest extends PapiTestCase
{
    public function test(): void
    {
        $request = $this->createRequest(HttpMethod::DELETE, "/");
        $builder = new PapiBuilder();
        $app = $builder->addAction(FooDelete::class)->build();
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
