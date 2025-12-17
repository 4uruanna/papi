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

use function DI\create;

$definitions = [ FooDefinition::class => create()->constructor() ];

ApiBuilder::getInstance()
    ->setActions(FooGet::class)
    ->setDefinition($definitions)
    ->setEvents(FooEvent::class, BarEvent::class)
    ->setModules(FooModule::class, BarModule::class)
    ->build()
    ->run();
```

### Samples

- [Action](./test/abstract/PapiGetTest.php)
- [Definition](https://www.slimframework.com/docs/v4/concepts/di.html)
- [Event](./test/mock/FooEvent.php)
- [Middleware](./test/mock/BarMiddleware.php)
- [Module](./test/mock/FooModule.php)

### Events

| Order | Phase                     | Options                                   |
|:-:    |:-                         |:-                                         |
| 1     | `START`                   | []                                        |
| 2     | `CONFIGURE_DEFINITIONS`   | [container_builder ➡ ContainerBuilder, definitions ➡ array] |
| 3     | `CONFIGURE_MIDDLEWARES`   | [app ➡ App, middlewares ➡ array]          |
| 4     | `CONFIGURE_ACTIONS`       | [app ➡ App, actions ➡ array]              |
| 5     | `END`                     | [app ➡ App]                               |


## Modules Availables

| Name          | Github    | Packagist |
|:-             |:-         |:-         |
| Cache         | [GitHub](https://github.com/4uruanna/papimod-cache)       | [Packagist](https://packagist.org/packages/papimod/cache) |
| CORS          | [GitHub](https://github.com/4uruanna/papimod-cache)       | [Packagist](https://packagist.org/packages/papimod/cache) |
| CORS          | [GitHub](https://github.com/4uruanna/papimod-cors)        | [Packagist](https://packagist.org/packages/papimod/cors) |
| Date          | [GitHub](https://github.com/4uruanna/papimod-date)        | [Packagist](https://packagist.org/packages/papimod/date) |
| DotEnv        | [GitHub](https://github.com/4uruanna/papimod-dotenv)      | [Packagist](https://packagist.org/packages/papimod/dotenv) |
| Http Error    | [GitHub](https://github.com/4uruanna/papimod-http-error)  | [Packagist](https://packagist.org/packages/papimod/http-error) |

## Test

```shell
composer test
```

### Call API From Test

```Php
use Papi\Test\PapiTestCase;
use Papi\Test\mock\FooGet;
use PHPUnit\Framework\Attributes\CoversClass;

class CustomTest extends PapiTestCase
{
    public function testLoadActions(): void
    {
        $body = [ "attribute" => 1 ];
        $request = $this->createRequest('GET', '/foo', $body);

        $builder = new PapiBuilder();
        $response = $builder->setActions([FooGet::class])->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("foo", (string) $response->getBody());
    }
}
```

## Resources

- __[Slim](https://www.slimframework.com/docs/v4/)__
    - [Routing](https://www.slimframework.com/docs/v4/objects/routing.html#custom-route)
    - [Middleware](https://www.slimframework.com/docs/v4/concepts/middleware.html)
    - [Dependency Container](https://www.slimframework.com/docs/v4/concepts/di.html)
- __[Php-di](https://php-di.org/)__

- Thanks to [Mauro Bonfietti](https://discourse.slimframework.com/u/maurobonfietti/summary) for his contribution to the community. [Writing PHP Unit Tests](https://discourse.slimframework.com/t/migrating-from-slim-3-to-4-writing-php-unit-tests/4193)