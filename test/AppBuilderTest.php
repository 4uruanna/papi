<?php

declare(strict_types=1);

namespace Papi\Test;

use Mockery;
use PHPUnit\Framework\TestCase;
use Papi\AppBuilder;
use Papi\enumerator\AppBuilderEvents;
use Papi\Test\foo\FooModule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use Slim\App;

#[CoversClass(AppBuilder::class)]
#[Small]
final class AppBuilderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testLoadModule(): void
    {
        $module = Mockery::mock(FooModule::class);

        $module->shouldReceive('getMiddlewares')->andReturn(false);
        $module->shouldReceive('getDefinitions')->andReturn(false);
        $module->shouldReceive('getRoutes')->andReturn(false);
        $module->shouldReceive('getEvents')->andReturn(false);

        $appBuilder = new AppBuilder();
        $app = $appBuilder
            ->setModules([$module])
            ->build();

        $this->assertInstanceOf(App::class, $app);
    }

    public function testLoadCallableEvent(): void
    {
        $bool = false;

        $module = Mockery::mock(FooModule::class);
        $module->shouldReceive('getMiddlewares')->andReturn(false);
        $module->shouldReceive('getDefinitions')->andReturn(false);
        $module->shouldReceive('getRoutes')->andReturn(false);
        $module->shouldReceive('getEvents')->andReturn([
            [
                AppBuilderEvents::BEFORE,
                function () use (&$bool) {
                    $bool = true;
                }
            ]
        ]);

        $appBuilder = new AppBuilder();
        $app = $appBuilder
            ->setModules([$module])
            ->build();

        $this->assertInstanceOf(App::class, $app);
        $this->assertTrue($bool);
    }
}
