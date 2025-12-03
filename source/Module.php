<?php

namespace Papi;

abstract class Module
{
    abstract protected string $path { get; }

    /**
     * Load middlewares
     * 
     * [Slim documentation: Middleware](https://www.slimframework.com/docs/v4/concepts/middleware.html)
     * @return \Psr\Http\Server\MiddlewareInterface[]|false
     */
    public function getMiddlewares(): array|false
    {
        return $this->requireFileIfExists('middlewares.php');
    }

    /**
     * Load definitions
     * 
     * [Slim documentation: Dependency Container](https://www.slimframework.com/docs/v4/concepts/di.html)
     * and [Php-di documentation](https://php-di.org/)
     * @return array|false
     */
    public function getDefinitions(): array|false
    {
        return $this->requireFileIfExists('definitions.php');
    }

    /**
     * Load routes
     * 
     * [Slim documentation: Routing](https://www.slimframework.com/docs/v4/objects/routing.html#custom-route)
     */
    public function getRoutes(): array|false
    {
        return $this->requireFileIfExists('routes.php');
    }

    /**
     * Load events
     */
    public function getEvents(): array|false
    {
        return $this->requireFileIfExists('events.php');
    }

    private function requireFileIfExists(string $file_name): array|false
    {
        $file_path = $this->path . DIRECTORY_SEPARATOR . $file_name;

        if ($file_path) {
            return require $file_path;
        }

        return false;
    }
}
