<?php

namespace Papi\Test\abstract;

use Papi\abstract\PapiPost;
use Papi\enumerator\HttpMethod;
use Papi\PapiBuilder;
use Papi\Test\mock\FooPost;
use Papi\Test\PapiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PapiPost::class)]
final class PapiPostTest extends PapiTestCase
{
    public function test(): void
    {
        $request = $this->createRequest(HttpMethod::POST, "/");
        $builder = new PapiBuilder();
        $app = $builder->addAction(FooPost::class)->build();
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
