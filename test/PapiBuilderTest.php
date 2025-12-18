<?php

namespace Papi\Test;

use Papi\PapiBuilder;
use Papi\Test\mock\BarModule;
use Papi\Test\mock\FooGet;
use Papi\Test\mock\FooDefinition;
use Papi\Test\mock\FooEvent;
use Papi\Test\mock\FooMiddleware;
use Papi\Test\mock\FooModule;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionClass;
use Slim\App;

use function DI\create;

#[CoversClass(PapiBuilder::class)]
final class PapiBuilderTest extends PapiTestCase
{
    private PapiBuilder $builder;

    public function setUp(): void
    {
        parent::setUp();
        $this->builder = new PapiBuilder();
    }

    public function testAddAction(): void
    {
        $this->builder->addAction(FooGet::class);
        $reflection = new ReflectionClass(PapiBuilder::class);
        $actions = $reflection->getProperty("actions")->getValue($this->builder);
        $this->assertContains(FooGet::class, $actions);
    }

    public function testAddDefinition(): void
    {
        $this->builder->addDefinition([FooDefinition::class => create()->constructor()]);
        $reflection = new ReflectionClass(PapiBuilder::class);
        $definitions = $reflection->getProperty("definitions")->getValue($this->builder);
        $this->assertArrayHasKey(FooDefinition::class, $definitions);
    }

    public function testAddEvent(): void
    {
        $this->builder->addEvent(FooEvent::class);
        $reflection = new ReflectionClass(PapiBuilder::class);
        $events = $reflection->getProperty("events")->getValue($this->builder);
        $this->assertArrayHasKey(FooEvent::class, $events);
    }

    public function testAddMiddleware(): void
    {
        $this->builder->addMiddleware(FooMiddleware::class);
        $reflection = new ReflectionClass(PapiBuilder::class);
        $middlewares = $reflection->getProperty("middlewares")->getValue($this->builder);
        $this->assertContains(FooMiddleware::class, $middlewares);
    }

    public function testAddModule(): void
    {
        $app = $this->builder
            ->addModule(
                FooModule::class,
                BarModule::class
            )
            ->build();

        $reflection = new ReflectionClass(PapiBuilder::class);
        $modules = $reflection->getProperty("modules")->getValue($this->builder);
        $this->assertContains(FooModule::class, $modules);

        $actions = $reflection->getProperty("actions")->getValue($this->builder);
        $this->assertContains(FooGet::class, $actions);

        $definitions = $reflection->getProperty("definitions")->getValue($this->builder);
        $this->assertArrayHasKey(FooDefinition::class, $definitions);

        $events = $reflection->getProperty("events")->getValue($this->builder);
        $this->assertArrayHasKey(FooEvent::class, $events);

        $middlewares = $reflection->getProperty("middlewares")->getValue($this->builder);
        $this->assertContains(FooMiddleware::class, $middlewares);

        $this->assertInstanceOf(App::class, $app);
    }

    public function testFailLoadPrerequisites(): void
    {
        $app = $this->builder
            ->addModule(BarModule::class)
            ->build();

        $this->assertFalse($app);
    }
}
