<?php

namespace Dos0\Framework\Middleware;

use Dos0\Framework\DI\DIInjector;
use Dos0\Framework\Middlewar\Exception\MiddlewareClassNotExistException;
use Dos0\Framework\Middleware\Exception\MiddlewareKeyNotExistException;
use Dos0\Framework\Middleware\Rules\MiddlewareInterface;
use Dos0\Framework\Request\Request;

class Middleware
{
    /**
     * @var Request
     */
    private $request;

    /**
     * Middleware config map
     *
     * @var array
     */
    private $middlewareMap = [];

    /**
     * Middleware route keys
     *
     * @var array
     */
    private $routeMiddlewares = [];

    /**
     * Middleware constructor.
     */
    public function __construct()
    {
        $this->request          = DIInjector::get('Request');
        $this->middlewareMap    = DIInjector::getConfig()['middlewareMap'];
        $this->routeMiddlewares = DIInjector::get('Route')->getMiddleware();

        $this->runMiddleware();
    }

    /**
     * Execute Middleware class
     *
     * @throws MiddlewareClassNotExistException
     * @throws MiddlewareKeyNotExistException
     */
    private function runMiddleware() {
        if (count($this->routeMiddlewares)) {
            foreach ($this->routeMiddlewares as $middlewareKey) {
                if (!array_key_exists($middlewareKey, $this->middlewareMap)) {
                    throw new MiddlewareKeyNotExistException('Middleware Key '.$middlewareKey.' Not Exist Exception');
                }
                $middlewareClass = $this->middlewareMap[$middlewareKey];
                if (!class_exists($middlewareClass)) {
                    throw new MiddlewareClassNotExistException('Middleware Class '.$middlewareClass.' Not Exist Exception');
                }

                /* @var MiddlewareInterface $middleware */
                $middleware = new $middlewareClass();
                $middleware->handle($this->request);
            }
        }
    }
}