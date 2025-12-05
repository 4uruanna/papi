<?php

declare(strict_types=1);

namespace Papi\Test;

use Papi\AppBuilder;
use Papi\Test\foo\FooModule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use Slim\App;
use Slim\Exception\HttpNotFoundException;

#[CoversClass(AppBuilder::class)]
#[Small]
final class AppBuilderTest extends ApiTestCase
{
    protected App $app;

    public function setUp(): void
    {
        $this->app = new AppBuilder()
            ->setModules([FooModule::class])
            ->build();
    }

    public function testHome(): void
    {
        $request = $this->createRequest('GET', '/');
        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNotFound(): void
    {
        $request = $this->createRequest('GET', '/notFoundEndpoint');
        $this->expectException(HttpNotFoundException::class);
        $response = $this->app->handle($request);
    }
}
