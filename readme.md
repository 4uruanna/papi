# Papi


![]( https://img.shields.io/badge/php-8.5-777BB4?logo=php)
![]( https://img.shields.io/badge/composer-2-885630?logo=composer)

## Description

Help with creating new [Slim](https://www.slimframework.com/) API.
Distributed under the [Apache-2.0](./license) license.

## Installation

```shell
composer require papi/papi
```

## Usage

```php
require __DIR__ . "/../vendor/autoload.php";

ApiBuilder::getInstance()
    ->setEvents([ FooEvent::class, BarEvent::class ])
    ->setModules([ FooModule::class, BarModule::class ])
    ->setDefinition([ FooService::class => create()->constructor() ])
    ->setActions([ FooAction::class ])
    ->build()
    ->run();
```

### Samples

#### Events
```Php
use Papi\enumerator\EventPhases;
use Papi\Event;

class FooEvent implements Event
{
    public static function getPhase(): string
    {
        return EventPhases::AFTER_BUILD_DI;
    }

    public function __invoke(mixed ...$args): void
    {
        // ...
    }
}
```

| Order | Phase                 | Arguments                             |
|:-:    |:-                     |:-                                     |
| 1     | `BEFORE_BUILD`        | ()                                    |
| 2     | `BEFORE_BUILD_DI`     | (ContainerBuilder $builder)           |
| 3     | `AFTER_BUILD_DI`      | (Container $container)                |
| 4     | `BEFORE_MIDDLEWARES`  | (App $app, array $middleware_list)    |
| 5     | `AFTER_MIDDLEWARES`   | (App $app)                            |
| 6     | `BEFORE_ACTIONS`      | (App $app, array $routes)             |
| 7     | `AFTER_ACTIONS`       | (App $app)                            |
| 8     | `AFTER_BUILD`         | (App $app)                            |

#### Actions

```Php
use Papi\Action;
use Papi\enumerator\HttpMethods;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class FooAction implements Action
{
    public static function getMethod(): string
    {
        return HttpMethods::GET;
    }

    public static function getPattern(): string
    {
        return '/foo';
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $response
            ->withStatus(200)
            ->getBody()
            ->write("foo");
        return $response;
    }
}
```

#### Middleware

```Php
use Papi\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class FooMiddlewareBefore implements Middleware
{
    public static function getPriority(): int
    {
        return 1;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // ...
        return $handler->handle($request);
    }
}

```

#### Module

```Php
class FooModule extends ApiModule
{
    public function __construct()
    {
        $this->action_list = [ FooAction::class ];

        $this->definition_list = [ FooService::class => create()->constructor() ];

        $this->event_list = [ FooEvent::class ];

        $this->middleware_list = [ FooMiddleWare::class ];

        $this->prerequisite_list [ BarModule::class ];
    }

    public function configure()
    {
        defined("CUSTOM_OPTION") || define("CUSTOM_OPTION", true);
    }
}

```

## Test

```shell
composer test
```

### Call API Test

```Php
use Papi\Test\ApiBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FooModule::class)]
class ApiBuilderTest extends ApiBaseTestCase
{
    public function testLoadActions(): void
    {
        $request = $this->createRequest('GET', '/foo')
            ->withParsedBody([ /* ... */ ]);

        $response = ApiBuilder::getInstance()
            ->setActions([FooAction::class])
            ->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("foo", (string) $response->getBody());
    }
}
```

## Modules Availables

## Resources

- __Official Modules__
    - __Cache :__ [GitHub](https://github.com/4uruanna/papimod-cache), [Packagist](https://packagist.org/packages/papimod/cache)
    - __CORS :__ [GitHub](https://github.com/4uruanna/papimod-cors), [Packagist](https://packagist.org/packages/papimod/cors)
    - __Date :__ [GitHub](https://github.com/4uruanna/papimod-date), [Packagist](https://packagist.org/packages/papimod/date)
    - __DotEnv :__ [GitHub](https://github.com/4uruanna/papimod-dotenv), [Packagist](https://packagist.org/packages/papimod/dotenv)
    - __Http Error :__ [GitHub](https://github.com/4uruanna/papimod-http-error), [Packagist](https://packagist.org/packages/papimod/http-error)
- __[Slim](https://www.slimframework.com/docs/v4/)__
    - [Routing](https://www.slimframework.com/docs/v4/objects/routing.html#custom-route)
    - [Middleware](https://www.slimframework.com/docs/v4/concepts/middleware.html)
    - [Dependency Container](https://www.slimframework.com/docs/v4/concepts/di.html)
- __[Php-di](https://php-di.org/)__

- Thanks to [Mauro Bonfietti](https://discourse.slimframework.com/u/maurobonfietti/summary) for his contribution to the community. [Writing PHP Unit Tests](https://discourse.slimframework.com/t/migrating-from-slim-3-to-4-writing-php-unit-tests/4193)