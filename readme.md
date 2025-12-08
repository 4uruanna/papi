# Papi

## Description

Help with creating new [Slim](https://www.slimframework.com/) API.
Distributed under the [Apache-2.0](./license) license.

## Requirements

| Name      | Version   |
| :-        | :-        |
| Php       | 8.5       |
| Composer  | 2         |

## Installation

```shell
composer require papi/papi
```

## Usage

```php
require __DIR__ . "/../vendor/autoload.php";

new AppBuilder()
    ->setModules([ /* Add modules here */ ])
    ->setDefinition([ /* Custom definitions */ ])
    ->setActions([ /* Custom actions */ ])
    ->setEvents([ /* Custom listeners */ ])
    ->build()
    ->run();
```
## Test

```shell
composer test
```

## Resources

- [Slim documentation: Routing](https://www.slimframework.com/docs/v4/objects/routing.html#custom-route)
- [Slim documentation: Middleware](https://www.slimframework.com/docs/v4/concepts/middleware.html)
- DI
    - [Slim documentation: Dependency Container](https://www.slimframework.com/docs/v4/concepts/di.html)
    - [Php-di documentation](https://php-di.org/)