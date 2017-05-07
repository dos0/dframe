<?php
namespace Dos0\Framework;

use Dos0\Framework\Exception\ControllerIsNotFoundException;
use Dos0\Framework\Exception\MethodOfControllerIsNotFoundException;
use Dos0\Framework\Request\Request;
use Dos0\Framework\Response\ResponsePrepare;
use Dos0\Framework\Router\Route;
use Dos0\Framework\Router\Router;

/**
 * Class Application
 * @package Dos0\Framework
 */
class Application
{
    private $config;
    private $request;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function run()
    {
        $this->request = new Request();

        try {
            $router = new Router($this->config);

            $route = $router->getRoute($this->request);

            $controllerResult = $this->getControllerResult($route);

            $responsePrepare = new ResponsePrepare($this->request);
            $responsePrepare->setData($controllerResult);

            $response = $responsePrepare->make();

            $response->send();

            //return $response;

        } catch (\Exception $e) {

            echo "<h3>Exception: {$e->getMessage()} </h3>\n";
        }

    }

    public function __destruct()
    {
        //@TODO: Close active connections, etc.
    }

    private function getControllerResult(Route $route)
    {
        if (!class_exists($route->getController())) {
            throw new ControllerIsNotFoundException("Controller '{$route->getController()}' Is Not Found");
        }
        $reflectionClass = new \ReflectionClass($route->getController());

        if (!$reflectionClass->hasMethod($route->getMethod())) {
            throw new MethodOfControllerIsNotFoundException(
                "Method '{$route->getMethod()}' Of Controller '{$route->getController()}' Is Not Found");
        }

        $controller = $reflectionClass->newInstance();
        $reflectionMethod = $reflectionClass->getMethod($route->getMethod());

        return $reflectionMethod->invokeArgs($controller, $route->getParams());
    }
};