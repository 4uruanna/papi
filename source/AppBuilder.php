<?php

namespace Papi;

use DI\Bridge\Slim\Bridge;
use DI\Container;
use DI\ContainerBuilder;
use Papi\enumerator\EventPhases;
use Papi\enumerator\HttpMethods;
use Papi\error\NotImplementedException;
use Slim\App;

final class AppBuilder
{
    private array $definition_list = [];

    /** @var Middleware[][] */
    private array $middleware_list = [];

    /** @var Action[] */
    private array $action_list = [];

    /** @var Event[][] */
    private array $event_map = [];

    /**
     * Reset the builder
     *
     * @return AppBuilder
     */
    public function reset(): AppBuilder
    {
        $this->middleware_list = [];
        $this->definition_list = [];
        $this->action_list = [];
        $this->event_map = [];
        return $this;
    }

    /**
     * Add api event listener
     *
     * @param int $event \Papi\enumerator\EventPhases
     * @param string[] $event_list
     * @return AppBuilder
     */
    public function setEvents(array $event_list): AppBuilder
    {
        $length = count($event_list);

        for ($index = 0; $index < $length; $index++) {
            /** @var Event */
            $event = new $event_list[$index]();

            if (!isset($this->event_map[$event->phase])) {
                $this->event_map[$event->phase] = [];
            }

            $this->event_map[$event->phase][] = $event;
        }

        return $this;
    }

    /**
     * Set api middlewares
     *
     * @param string[] $middleware_list
     * @return AppBuilder
     */
    public function setMiddlewares(array $middleware_list): AppBuilder
    {
        $length = count($middleware_list);

        for ($index = 0; $index < $length; $index++) {
            /** @var Middleware */
            $middleware = new $middleware_list[$index]();

            if (!isset($this->middleware_list[$middleware->priority])) {
                $this->middleware_list[$middleware->priority] = [];
            }

            $this->middleware_list[$middleware->priority][] = $middleware;
        }

        return $this;
    }

    /**
     * Set dependencies definitions
     *
     * @param array $definition_list
     * @return AppBuilder
     */
    public function setDefinitions(array $definition_list): AppBuilder
    {
        $length = count($definition_list);

        for ($index = 0; $index < $length; $index++) {
            $this->definition_list[] = $definition_list[$index];
        }

        return $this;
    }

    /**
     * Set api actions
     *
     * @param Action[] $action_list
     * @return AppBuilder
     */
    public function setActions(array $action_list): AppBuilder
    {
        $length = count($action_list);

        for ($index = 0; $index < $length; $index++) {
            $this->action_list[] = new $action_list[$index]();
        }

        return $this;
    }

    /**
     * Set application modules
     *
     * @param string[] $module_list
     * @return AppBuilder
     */
    public function setModules(array $module_list): AppBuilder
    {
        $length = count($module_list);

        for ($index = 0; $index < $length; $index++) {
            $instance = new $module_list[$index]();

            if (($instance instanceof Module) === false) {
                throw new NotImplementedException();
            }

            if ($middlewares = $instance->getMiddlewares()) {
                $this->setMiddlewares($middlewares);
            }

            if ($definitions = $instance->getDefinitions()) {
                $this->setDefinitions($definitions);
            }

            if ($routes = $instance->getActions()) {
                $this->setActions($routes);
            }

            if ($events = $instance->getEvents()) {
                $this->setEvents($events);
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
        $this->callEvents(EventPhases::BEFORE);

        $container = $this->loadContainer();

        $app = Bridge::create($container);

        $this->loadMiddlewares($app);

        $this->loadActions($app);

        $this->callEvents(EventPhases::AFTER, $app);

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

        $this->callEvents(EventPhases::BEFORE_DI, $container_builder);

        $container_builder->addDefinitions($this->definition_list);

        $container = $container_builder->build();

        $this->callEvents(EventPhases::AFTER_DI, $container);

        return $container;
    }

    /**
     * Load application middlewares
     *
     * @param App $app
     * @return void
     */
    private function loadMiddlewares(App $app): void
    {
        $this->callEvents(EventPhases::BEFORE_MIDDLEWARES, $app, $this->middleware_list);

        $priority_list = array_keys($this->middleware_list);
        $length = count(array_keys($this->middleware_list));

        for ($index = 0; $index < $length; $index++) {
            $priority = $priority_list[$index];
            $length_list = count($this->middleware_list[$priority]);

            for ($index_list = 0; $index_list < $length_list; $index_list++) {
                $app->add($this->middleware_list[$priority][$index_list]);
            }
        }

        $this->callEvents(EventPhases::AFTER_MIDDLEWARES, $app);
    }

    /**
     * Load application actions
     *
     * @param App $app
     * @return void
     */
    private function loadActions(App $app): void
    {
        $this->callEvents(EventPhases::BEFORE_ACTIONS, $app, $this->action_list);

        $length = count($this->action_list);

        for ($index = 0; $index < $length; $index++) {
            $pattern = $this->action_list[$index]->pattern;
            $http_method = $this->action_list[$index]->http_method;

            switch ($http_method) {
                case HttpMethods::GET:
                    $app->get($pattern, [$this->action_list[$index]::class, '__invoke']);
                    break;

                case HttpMethods::POST:
                    $app->post($pattern, [$this->action_list[$index]::class, '__invoke']);
                    break;

                case HttpMethods::PUT:
                    $app->put($pattern, [$this->action_list[$index]::class, '__invoke']);
                    break;

                case HttpMethods::PATCH:
                    $app->patch($pattern, [$this->action_list[$index]::class, '__invoke']);
                    break;

                case HttpMethods::DELETE:
                    $app->delete($pattern, [$this->action_list[$index]::class, '__invoke']);
                    break;
            }
        }

        $this->callEvents(EventPhases::AFTER_ACTIONS, $app);
    }

    /**
     * Call application events
     *
     * @param int $event \Papi\enumerator\EventPhases
     * @param mixed ...$args
     * @return void
     */
    private function callEvents(int $event, mixed ...$args): void
    {
        if (isset($this->event_map[$event])) {
            $length = count($this->event_map[$event]);

            for ($index = 0; $index < $length; $index++) {
                $this->event_map[$event][$index](...$args);
            }
        }
    }
}
