<?php

namespace Papi;

use DI\Bridge\Slim\Bridge;
use DI\Container;
use DI\ContainerBuilder;
use Papi\enumerator\EventType;
use Papi\interface\PapiAction;
use Papi\interface\PapiEventListener;
use Slim\App;

final class PapiBuilder
{
    private array $actions;

    private array $definitions;

    private array $events;

    private array $middlewares;

    private array $modules;

    public function __construct()
    {
        $this->actions = [];
        $this->definitions = [];
        $this->events = [];
        $this->middlewares = [];
        $this->modules = [];
    }

    public function build(): App|false
    {
        $app = false;

        $this->notify(EventType::START, []);

        if ($this->setupModules()) {
            $container = $this->setupDefinitions();

            $app = Bridge::create($container);

            $this->setupMiddlewares($app);

            $this->setupActions($app);

            $this->notify(EventType::END, ['app' => $app]);
        }

        return $app;
    }

    public function addAction(string ...$actions): self
    {
        array_push($this->actions, ...$actions);
        return $this;
    }

    public function addDefinition(array $definitions): self
    {
        $this->definitions = array_merge($this->definitions, $definitions);
        return $this;
    }

    public function addEvents(string ...$events): self
    {
        foreach ($events as $event) {
            $interfaces = class_implements($event);
            if (isset($interfaces[PapiEventListener::class])) {
                $this->events[$event] = new $event();
            }
        }
        return $this;
    }

    public function addMiddlewares(string ...$middlewares): self
    {
        array_push($this->middlewares, ...$middlewares);
        return $this;
    }

    public function addModules(string ...$modules): self
    {
        array_push($this->modules, ...$modules);
        return $this;
    }

    private function notify(EventType $event_type, array $options): void
    {
        foreach ($this->events as $event) {
            $event($event_type, $options);
        }
    }

    private function setupActions(App $app): void
    {
        $this->notify(
            EventType::CONFIGURE_ACTIONS,
            [
                'app' => $app,
                'actions' => $this->actions
            ]
        );

        foreach ($this->actions as $pattern => $action) {
            $interfaces = class_implements($action);

            if (isset($interfaces[PapiAction::class])) {
                $method = $action::getMethod()->value;
                $pattern = $action::getPattern();
                $app->$method($pattern, $action);
            }
        }
    }

    private function setupDefinitions(): Container
    {
        $container_builder = new ContainerBuilder();

        $this->notify(
            EventType::CONFIGURE_DEFINITIONS,
            [
                'container_builder' => $container_builder,
                'definitions' => $this->definitions
            ]
        );

        $container_builder->addDefinitions($this->definitions);

        return $container_builder->build();
    }

    private function setupMiddlewares(App $app): void
    {
        $this->notify(
            EventType::CONFIGURE_MIDDLEWARES,
            [
                'app' => $app,
                'middlewares' => $this->middlewares
            ]
        );

        $middlewares_map = [];

        while (count($this->middlewares) !== 0) {
            foreach ($this->middlewares as $middleware) {
                if (isset($middlewares_map[$middleware]) === false) {
                    if ($middleware::register($app, $middlewares_map)) {
                        $middlewares_map[$middleware] = true;
                        $app->add($middleware);
                    }
                }
            }

            $this->middlewares = array_filter(
                $this->middlewares,
                fn($m) => isset($middlewares_map[$m]) === false
            );
        }

        $this->middlewares = array_keys($middlewares_map);
    }

    private function setupModules(): bool
    {
        $result = true;

        foreach ($this->modules as $module_class) {
            if (is_subclass_of($module_class, PapiModule::class)) {
                $prerequisites = $module_class::getPrerequisites();

                foreach ($prerequisites as $prerequisite) {
                    if (isset($this->modules[$prerequisite]) === false) {
                        $result = false;
                    }
                }

                $module_class::configure();
                $this->modules[$module_class] = true;
                $this->addDefinition($module_class::getDefinitions());
                $this->addEvents(...$module_class::getEvents());
                $this->addAction(...$module_class::getActions());
                $this->addMiddlewares(...$module_class::getMiddlewares());
            }
        }

        return $result;
    }
}
