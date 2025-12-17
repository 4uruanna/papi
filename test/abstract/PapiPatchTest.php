<?php

namespace Papi\Test\abstract;

use Papi\abstract\PapiPatch;
use Papi\enumerator\HttpMethod;
use Papi\PapiBuilder;
use Papi\Test\mock\FooPatch;
use Papi\Test\PapiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PapiPatch::class)]
final class PapiPatchTest extends PapiTestCase
{
    public function test(): void
    {
        $request = $this->createRequest(HttpMethod::PATCH, "/");
        $builder = new PapiBuilder();
        $app = $builder->addAction(FooPatch::class)->build();
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
