<?php

namespace Papi\enumerator;

final class EventPhases
{
    public const BEFORE_BUILD = "before_build"; # () : void
    public const AFTER_BUILD = "after_build"; # (App $app) : void

    public const BEFORE_BUILD_DI = "before_di"; # (ContainerBuilder $builder) : void
    public const AFTER_BUILD_DI = "after_di"; # (Container $container) : void

    public const BEFORE_MIDDLEWARES = "before_middlewares"; # (App $app, array $middleware_list) : void
    public const AFTER_MIDDLEWARES = "after_middlewares"; # (App $app) : void

    public const BEFORE_ACTIONS = "before_actions"; # (App $app, array $routes) : void
    public const AFTER_ACTIONS = "after_actions"; # (App $app) : void
}
