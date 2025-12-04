<?php

namespace Papi;

use DI\Bridge\Slim\Bridge;
use DI\Container;
use DI\ContainerBuilder;
use Papi\enumerator\AppBuilderEvents;
use Slim\App;

final class AppBuilder
{
    private array $middlewares = [];

    private array $definitions = [];

    private array $routes = [];

    private array $events = [];

    /**
     * Reset the builder
     *
     * @return AppBuilder
     */
    public function reset(): AppBuilder
    {
        $this->middlewares = [];
        $this->definitions = [];
        $this->routes = [];
        $this->events = [];
        return $this;
    }

    /**
     * Add application event listener
     *
     * @param int $event
     * @param array|string $callback
     * @return AppBuilder
     */
    public function on(int $event, array|string|callable $callback): AppBuilder
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }

        $this->events[$event][] = $callback;
        return $this;
    }

    /**
     * Set appliation middlewares
     *
     * @param array $middlewares
     * @return AppBuilder
     */
    public function setMiddlewares(array $middlewares): AppBuilder
    {
        foreach ($middlewares as $middleware) {
            $this->middlewares[] = $middleware;
        }
        return $this;
    }

    /**
     * Set DI definitions
     *
     * @param array $definitions
     * @return AppBuilder
     */
    public function setDefinitions(array $definitions): AppBuilder
    {
        foreach ($definitions as $class => $definition) {
            $this->definitions[$class] = $definition;
        }
        return $this;
    }

    /**
     * Set application routes
     *
     * @param array $routes
     * @return AppBuilder
     */
    public function setRoutes(array $routes): AppBuilder
    {
        foreach ($routes as $route) {
            $this->routes[] = $route;
        }
        return $this;
    }

    /**
     * Set application modules
     *
     * @param \Papi\Module[] $modules
     * @return AppBuilder
     */
    public function setModules(array $modules): AppBuilder
    {
        foreach ($modules as $module) {
            if ($middlewares = $module->getMiddlewares()) {
                $this->setMiddlewares($middlewares);
            }

            if ($definitions = $module->getDefinitions()) {
                $this->setDefinitions($definitions);
            }

            if ($routes = $module->getRoutes()) {
                $this->setRoutes($routes);
            }

            if ($events = $module->getEvents()) {
                foreach ($events as $event) {
                    $this->on(...$event);
                }
            }
        }
        return $this;
    }

    /**
     * Build new slim application
     *
     * @return App
     */
    public function build(): App
    {
        $this->callEvents(AppBuilderEvents::BEFORE);

        $container = $this->loadContainer();

        $app = Bridge::create($container);

        $this->loadMiddlewares($app);

        $this->loadRoutes($app);

        $this->callEvents(AppBuilderEvents::AFTER, $app);

        return $app;
    }

    /**
     * Create and build di container
     *
     * @return Container
     */
    private function loadContainer(): Container
    {
        $container_builder = new ContainerBuilder();

        $this->callEvents(AppBuilderEvents::BEFORE_DI, $container_builder);

        $container_builder->addDefinitions($this->definitions);

        $container = $container_builder->build();

        $this->callEvents(AppBuilderEvents::AFTER_DI, $container);

        return $container;
    }

    /**
     * Load application middlewares
     */
    private function loadMiddlewares(App $app): void
    {
        $this->callEvents(AppBuilderEvents::BEFORE_MIDDLEWARES, $app, $this->middlewares);

        foreach ($this->middlewares as $middleware) {
            if (is_array($middleware)) {
                call_user_func($middleware[0], $app);
            } else {
                $app->add($middleware);
            }
        }

        $this->callEvents(AppBuilderEvents::AFTER_MIDDLEWARES, $app);
    }

    /**
     * Load application routes
     */
    private function loadRoutes(App $app): void
    {
        $this->callEvents(AppBuilderEvents::BEFORE_ROUTES, $app, $this->routes);

        foreach ($this->routes as $route) {
            $app->map(...$route);
        }

        $this->callEvents(AppBuilderEvents::AFTER_ROUTES, $app);
    }

    /**
     * Call application events
     */
    private function callEvents(int $event, mixed ...$args)
    {
        if (isset($this->events[$event])) {
            foreach ($this->events[$event] as $callback) {
                if (is_callable($callback)) {
                    $callback(...$args);
                } else {
                    call_user_func($callback, ...$args);
                }
            }
        }
    }
}
