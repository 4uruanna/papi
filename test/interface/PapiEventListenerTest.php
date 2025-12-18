<?php

namespace Papi\Test\interface;

use DI\ContainerBuilder;
use Papi\enumerator\EventType;
use Papi\interface\PapiEventListener;
use Papi\PapiBuilder;
use Papi\Test\mock\AllEvent;
use Papi\Test\PapiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionClass;
use Slim\App;

#[CoversClass(PapiEventListener::class)]
final class PapiEventListenerTest extends PapiTestCase
{
    private AllEvent $event;

    public function setUp(): void
    {
        $builder = new PapiBuilder();
        $builder->addEvent(AllEvent::class)->build();

        $reflection = new ReflectionClass(PapiBuilder::class);
        $events = $reflection->getProperty('events')->getValue($builder);
        $this->event = $events[AllEvent::class];
    }

    public function testStart(): void
    {
        $args = $this->event->args_map[EventType::START->value];
        $this->assertSame([], $args);
    }

    public function testConfigureDefinitions(): void
    {
        $args = $this->event->args_map[EventType::CONFIGURE_DEFINITIONS->value];
        $this->assertArrayHasKey("container_builder", $args);
        $this->assertInstanceOf(ContainerBuilder::class, $args["container_builder"]);

        $this->assertArrayHasKey("definitions", $args);
        $this->assertIsArray($args["definitions"]);
    }

    public function testConfigureMiddlewares(): void
    {
        $args = $this->event->args_map[EventType::CONFIGURE_MIDDLEWARES->value];
        $this->assertArrayHasKey("app", $args);
        $this->assertInstanceOf(App::class, $args["app"]);

        $this->assertArrayHasKey("middlewares", $args);
        $this->assertIsArray($args["middlewares"]);
    }

    public function testConfigureActions(): void
    {
        $args = $this->event->args_map[EventType::CONFIGURE_ACTIONS->value];
        $this->assertArrayHasKey("app", $args);
        $this->assertInstanceOf(App::class, $args["app"]);

        $this->assertArrayHasKey("actions", $args);
        $this->assertIsArray($args["actions"]);
    }

    public function testEnd(): void
    {
        $args = $this->event->args_map[EventType::END->value];
        $this->assertArrayHasKey("app", $args);
        $this->assertInstanceOf(App::class, $args["app"]);
    }
}
