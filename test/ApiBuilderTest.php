<?php

namespace Papi\Test;

use DI\Bridge\Slim\Bridge;
use DI\Container;
use DI\ContainerBuilder;
use Papi\ApiBuilder;
use Papi\error\MissingModuleException;
use Papi\Test\foo\actions\DiFooAction;
use Papi\Test\foo\actions\FooAction;
use Papi\Test\foo\events\FooEventAfterActions;
use Papi\Test\foo\events\FooEventAfterBuild;
use Papi\Test\foo\events\FooEventAfterDi;
use Papi\Test\foo\events\FooEventAfterMiddlewares;
use Papi\Test\foo\events\FooEventBeforeActions;
use Papi\Test\foo\events\FooEventBeforeBuild;
use Papi\Test\foo\events\FooEventBeforeDi;
use Papi\Test\foo\events\FooEventBeforeMiddlewares;
use Papi\Test\foo\FooInvalidModule;
use Papi\Test\foo\FooModule;
use Papi\Test\foo\FooValidModule;
use Papi\Test\foo\middlewares\DiFooMiddleware;
use Papi\Test\foo\middlewares\FooMiddlewareAfter;
use Papi\Test\foo\middlewares\FooMiddlewareBefore;
use Papi\Test\foo\services\FooService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use ReflectionClass;
use Slim\App;

use function DI\create;

#[CoversClass(ApiBuilder::class)]
#[Medium]
class ApiBuilderTest extends ApiBaseTestCase
{
    public function testGetInstance(): void
    {
        $builder = ApiBuilder::getInstance();
        $this->assertInstanceOf(ApiBuilder::class, $builder);
    }

    # ----------------------------------------- #
    # DÉFINITIONS                               #
    # ----------------------------------------- #

    public function testSetDefinitions(): void
    {
        $definitions = [FooService::class => create()->constructor()];
        $builder = ApiBuilder::getInstance()->setDefinitions($definitions);
        $reflection = new ReflectionClass(ApiBuilder::class);
        $value = $reflection->getProperty("definition_list")->getValue($builder);
        $this->assertArrayHasKey(FooService::class, $value);
        $this->assertEquals($definitions[FooService::class], $value[FooService::class]);
    }

    public function testLoadDefinitions(): void
    {
        $builder = ApiBuilder::getInstance()->setDefinitions([FooService::class => create()->constructor()]);
        $reflection = new ReflectionClass(ApiBuilder::class);
        $container = $reflection->getMethod("loadDefinitions")->invoke($builder);
        $this->assertInstanceOf(Container::class, $container);
    }

    # ----------------------------------------- #
    # ACTIONS                                   #
    # ----------------------------------------- #

    public function testSetActions(): void
    {
        $builder = ApiBuilder::getInstance()->setActions([FooAction::class]);
        $reflection = new ReflectionClass(ApiBuilder::class);
        $value = $reflection->getProperty("action_list")->getValue($builder);
        $this->assertEquals(1, count($value));
        $this->assertEquals(FooAction::class, $value[0]);
    }

    public function testLoadActions(): void
    {
        $builder = ApiBuilder::getInstance()->setActions([FooAction::class]);
        $app = Bridge::create();
        $reflection = new ReflectionClass(ApiBuilder::class);
        $reflection->getMethod("loadActions")->invoke($builder, $app);

        $request = $this->createRequest('GET', '/foo');
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("foo", (string) $response->getBody());
    }

    public function testActionDependencyInjection(): void
    {
        $app = ApiBuilder::getInstance()
            ->setDefinitions([FooService::class => create()->constructor()])
            ->setActions([DiFooAction::class])
            ->build();

        $request = $this->createRequest('GET', '/f00');
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("f00", (string) $response->getBody());
    }

    # ----------------------------------------- #
    # MIDDLEWARES                               #
    # ----------------------------------------- #

    public function testSetMiddlewares(): void
    {
        $middlewares = [
            FooMiddlewareBefore::class,
            FooMiddlewareAfter::class
        ];

        $builder = ApiBuilder::getInstance()->setMiddlewares($middlewares);
        $reflection = new ReflectionClass(ApiBuilder::class);
        $value = $reflection->getProperty("middleware_list")->getValue($builder);
        $this->assertEquals(2, count($value));
        $this->assertEquals(FooMiddlewareBefore::class, $value[FooMiddlewareBefore::getPriority()][0]);
        $this->assertEquals(FooMiddlewareAfter::class, $value[FooMiddlewareAfter::getPriority()][0]);
    }

    public function testLoadMiddlewares(): void
    {
        $middlewares = [
            FooMiddlewareAfter::class,
            FooMiddlewareBefore::class,
        ];


        $app = ApiBuilder::getInstance()
            ->setMiddlewares($middlewares)
            ->setActions([FooAction::class])
            ->build();

        $request = $this->createRequest('GET', '/foo');
        $app->handle($request);
        $this->assertLessThan(FooMiddlewareAfter::$execute_at, FooMiddlewareBefore::$execute_at);
    }

    public function testMiddlewareDependencyInjection(): void
    {
        $app = ApiBuilder::getInstance()
            ->setDefinitions([FooService::class => create()->constructor()])
            ->setActions([FooAction::class])
            ->setMiddlewares([DiFooMiddleware::class])
            ->build();

        $request = $this->createRequest('GET', '/foo');
        $app->handle($request);

        $reflection = new ReflectionClass(DiFooMiddleware::class);
        $value = $reflection->getStaticPropertyValue("fooService");
        $this->assertInstanceOf(FooService::class, $value);
    }

    # ----------------------------------------- #
    # EVENTS                                    #
    # ----------------------------------------- #

    public function testSetEvents(): void
    {
        $events = [
            FooEventBeforeBuild::class,
            FooEventAfterBuild::class
        ];

        $builder = ApiBuilder::getInstance()->setEvents($events);
        $reflection = new ReflectionClass(ApiBuilder::class);
        $value = $reflection->getProperty("event_list")->getValue($builder);

        $this->assertEquals(2, count($value));
        $this->assertInstanceOf(FooEventBeforeBuild::class, $value[FooEventBeforeBuild::getPhase()][0]);
        $this->assertInstanceOf(FooEventAfterBuild::class, $value[FooEventAfterBuild::getPhase()][0]);
    }

    public function testTriggerBeforeBuildEvent(): void
    {
        $app = ApiBuilder::getInstance()
            ->setEvents([FooEventBeforeBuild::class])
            ->setActions([FooAction::class])
            ->build();

        $request = $this->createRequest('GET', '/foo');
        $app->handle($request);

        $reflection = new ReflectionClass(FooEventBeforeBuild::class);
        $this->assertTrue($reflection->getStaticPropertyValue("loaded"));
    }

    public function testTriggerAfterBuildEvent(): void
    {
        $app = ApiBuilder::getInstance()
            ->setEvents([FooEventAfterBuild::class])
            ->setActions([FooAction::class])
            ->build();

        $request = $this->createRequest('GET', '/foo');
        $app->handle($request);

        $reflection = new ReflectionClass(FooEventAfterBuild::class);
        $this->assertTrue($reflection->getStaticPropertyValue("loaded"));
    }

    public function testTriggerBeforeDiEvent(): void
    {
        $app = ApiBuilder::getInstance()
            ->setEvents([FooEventBeforeDi::class])
            ->setActions([FooAction::class])
            ->build();

        $request = $this->createRequest('GET', '/foo');
        $app->handle($request);

        $reflection = new ReflectionClass(FooEventBeforeDi::class);
        $this->assertInstanceOf(ContainerBuilder::class, $reflection->getStaticPropertyValue("builder"));
    }

    public function testTriggerAfterDiEvent(): void
    {
        $app = ApiBuilder::getInstance()
            ->setEvents([FooEventAfterDi::class])
            ->setActions([FooAction::class])
            ->build();

        $request = $this->createRequest('GET', '/foo');
        $app->handle($request);

        $reflection = new ReflectionClass(FooEventAfterDi::class);
        $this->assertInstanceOf(Container::class, $reflection->getStaticPropertyValue("container"));
    }

    public function testTriggerBeforeActionsEvent(): void
    {
        $app = ApiBuilder::getInstance()
            ->setEvents([FooEventBeforeActions::class])
            ->setActions([FooAction::class])
            ->build();

        $request = $this->createRequest('GET', '/foo');
        $app->handle($request);

        $reflection = new ReflectionClass(FooEventBeforeActions::class);
        $this->assertInstanceOf(App::class, $reflection->getStaticPropertyValue("app"));
        $this->assertEquals(1, count($reflection->getStaticPropertyValue("route_list")));
    }

    public function testTriggerAfterActionsEvent(): void
    {
        $app = ApiBuilder::getInstance()
            ->setEvents([FooEventAfterActions::class])
            ->setActions([FooAction::class])
            ->build();

        $request = $this->createRequest('GET', '/foo');
        $app->handle($request);

        $reflection = new ReflectionClass(FooEventAfterActions::class);
        $this->assertInstanceOf(App::class, $reflection->getStaticPropertyValue("app"));
    }

    public function testTriggerBeforeMiddlewaresEvent(): void
    {
        $app = ApiBuilder::getInstance()
            ->setEvents([FooEventBeforeMiddlewares::class])
            ->setMiddlewares([FooMiddlewareBefore::class])
            ->setActions([FooAction::class])
            ->build();

        $request = $this->createRequest('GET', '/foo');
        $app->handle($request);

        $reflection = new ReflectionClass(FooEventBeforeMiddlewares::class);
        $this->assertInstanceOf(App::class, $reflection->getStaticPropertyValue("app"));
        $this->assertEquals(1, count($reflection->getStaticPropertyValue("middleware_list")));
    }

    public function testTriggerAfterMiddlewaresEvent(): void
    {
        $app = ApiBuilder::getInstance()
            ->setEvents([FooEventAfterMiddlewares::class])
            ->setMiddlewares([FooMiddlewareAfter::class])
            ->setActions([FooAction::class])
            ->build();

        $request = $this->createRequest('GET', '/foo');
        $app->handle($request);

        $reflection = new ReflectionClass(FooEventAfterMiddlewares::class);
        $this->assertInstanceOf(App::class, $reflection->getStaticPropertyValue("app"));
    }

    # ----------------------------------------- #
    # MODULES                                   #
    # ----------------------------------------- #

    public function testSetModule(): void
    {
        $builder = ApiBuilder::getInstance()->setModules([FooModule::class]);
        $reflection = new ReflectionClass(ApiBuilder::class);
        $value = $reflection->getProperty("module_list")->getValue($builder);
        $this->assertEquals(1, count($value));
        $this->assertEquals(FooModule::class, array_keys($value)[0]);

        $di_value = $reflection->getProperty("definition_list")->getValue($builder);
        $this->assertEquals(FooService::class, array_keys($di_value)[0]);
    }

    public function testSetInvalidModulePrerequisites(): void
    {
        $this->expectException(MissingModuleException::class);
        ApiBuilder::getInstance()->setModules([FooInvalidModule::class]);
    }

    public function testSetValidModulePrerequisites(): void
    {
        $builder = ApiBuilder::getInstance()->setModules([
            FooModule::class,
            FooValidModule::class
        ]);

        $reflection = new ReflectionClass(ApiBuilder::class);
        $value = $reflection->getProperty("module_list")->getValue($builder);
        $this->assertEquals(2, count($value));
    }
}
