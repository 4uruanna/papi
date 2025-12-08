<?php

namespace Papi;

use DI\Bridge\Slim\Bridge;
use DI\Container;
use DI\ContainerBuilder;
use Papi\enumerator\EventPhases;
use Papi\enumerator\HttpMethods;
use Papi\error\InvalidHttpMethodException;
use Papi\error\MissingModuleException;
use Slim\App;

final class ApiBuilder
{
    private static ApiBuilder|null $instance = null;

    public static function getInstance(): ApiBuilder
    {
        if (self::$instance === null) {
            self::$instance = new ApiBuilder();
        }

        self::$instance->definition_list = [];
        self::$instance->action_list = [];
        self::$instance->middleware_list = [];
        self::$instance->event_list = [];
        self::$instance->module_list = [];
        return self::$instance;
    }

    /**
     * Build new slim API
     *
     * @return App
     */
    public function build(): App
    {
        $this->triggerEvent(EventPhases::BEFORE_BUILD);
        $container = $this->loadDefinitions();
        $app = Bridge::create($container);
        $this->loadMiddlewares($app);
        $this->loadActions($app);
        $this->triggerEvent(EventPhases::AFTER_BUILD);
        return $app;
    }

    # ----------------------------------------- #
    # MODULES                                   #
    # ----------------------------------------- #

    private array $module_list = [];

    /**
     * Set API modules
     *
     * @param string[] $module_list
     * @return ApiBuilder
     */
    public function setModules(array $module_list): ApiBuilder
    {
        foreach ($module_list as $module) {
            $this->module_list[$module] = true;
            $instance = new $module();

            if ($instance->prerequisite_list !== null) {
                foreach ($instance->prerequisite_list as $prerequisite) {
                    if (isset($this->module_list[$prerequisite]) === false) {
                        throw new MissingModuleException(self::class, $prerequisite);
                    }
                }
            }

            $instance->configure();
            empty($instance->definition_list) || $this->setDefinitions($instance->definition_list);
            empty($instance->action_list) || $this->setActions($instance->action_list);
            empty($instance->event_list) || $this->setEvents($instance->event_list);
            empty($instance->middleware_list) || $this->setMiddlewares($instance->middleware_list);
        }
        return $this;
    }

    # ----------------------------------------- #
    # DÉFINITIONS                               #
    # ----------------------------------------- #

    private array $definition_list = [];

    /**
     * Set API dependencies
     *
     * @param array $definition_list
     * @return ApiBuilder
     */
    public function setDefinitions(array $definition_list): ApiBuilder
    {
        $this->definition_list += $definition_list;
        return $this;
    }

    /**
     * Build di container based on definitions
     *
     * @return Container
     */
    protected function loadDefinitions(): Container
    {
        $container_builder = new ContainerBuilder();
        $this->triggerEvent(EventPhases::BEFORE_BUILD_DI, $container_builder);
        $container_builder->addDefinitions($this->definition_list);
        $container = $container_builder->build();
        $this->triggerEvent(EventPhases::AFTER_BUILD_DI, $container);
        return $container;
    }

    # ----------------------------------------- #
    # ACTIONS                                   #
    # ----------------------------------------- #

    private array $action_list = [];

    /**
     * Set API actions
     *
     * @param string[] $action_list
     * @return ApiBuilder
     */
    public function setActions(array $action_list): ApiBuilder
    {
        array_push($this->action_list, ...$action_list);
        return $this;
    }

    /**
     * Load API actions
     *
     * @param App $app
     * @return void
     */
    protected function loadActions(App $app): void
    {
        $this->triggerEvent(EventPhases::BEFORE_ACTIONS, $app, $this->action_list);

        foreach ($this->action_list as $action_class) {
            $http_method = $action_class::getMethod();

            if (isset(HttpMethods::CALLBACK_MAP[$http_method])) {
                $app->{HttpMethods::CALLBACK_MAP[$http_method]}(
                    $action_class::getPattern(),
                    [$action_class, '__invoke']
                );
            } else {
                throw new InvalidHttpMethodException($action_class, $http_method);
            }
        }

        $this->triggerEvent(EventPhases::AFTER_ACTIONS, $app);
    }

    # ----------------------------------------- #
    # MIDDLEWARES                               #
    # ----------------------------------------- #

    private array $middleware_list = [];

    /**
     * Set API middlewares
     *
     * @param array $middleware_list
     * @return ApiBuilder
     */
    public function setMiddlewares(array $middleware_list): ApiBuilder
    {
        foreach ($middleware_list as $middleware) {
            $priority = $middleware::getPriority();

            if (isset($this->middleware_list[$priority]) === false) {
                $this->middleware_list[$priority] = [];
            }

            $this->middleware_list[$priority][] = $middleware;
        }

        return $this;
    }

    /**
     * Load API middlewares
     *
     * @param App $app
     * @return void
     */
    public function loadMiddlewares(App $app): void
    {
        $this->triggerEvent(EventPhases::BEFORE_MIDDLEWARES, $app, $this->middleware_list);

        foreach ($this->middleware_list as $middleware_list) {
            foreach ($middleware_list as $middleware) {
                $app->add($middleware);
            }
        }

        $this->triggerEvent(EventPhases::AFTER_MIDDLEWARES, $app);
    }

    # ----------------------------------------- #
    # EVENTS                                    #
    # ----------------------------------------- #

    private array $event_list = [];

    /**
     * Set build events
     *
     * @param array $event_list
     * @return ApiBuilder
     */
    public function setEvents(array $event_list): ApiBuilder
    {
        foreach ($event_list as $event) {
            $phase = $event::getPhase();

            if (isset($this->event_list[$phase]) === false) {
                $this->event_list[$phase] = [];
            }

            $this->event_list[$phase][] = new $event();
        }

        return $this;
    }

    /**
     * Trigger an event
     *
     * @param int $event_phase
     */
    public function triggerEvent(string $event_phase, mixed ...$args): void
    {
        if (isset($this->event_list[$event_phase])) {
            foreach ($this->event_list[$event_phase] as $event) {
                $event(...$args);
            }
        }
    }
}
