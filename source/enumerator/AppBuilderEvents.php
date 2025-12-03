<?php

namespace Papi\enumerator;

final class AppBuilderEvents
{
    public const BEFORE = 0; # () : void
    public const AFTER = 100; # (App $app) : void

    public const BEFORE_DI = 10; # (ContainerBuilder $builder) : void
    public const AFTER_DI = 20; # (Container $container) : void

    public const BEFORE_MIDDLEWARES = 30; # (App $app, array $middlewares) : void
    public const AFTER_MIDDLEWARES = 40; # (App $app) : void

    public const BEFORE_ROUTES = 50; # (App $app, array $routes) : void
    public const AFTER_ROUTES = 60; # (App $app) : void
}
