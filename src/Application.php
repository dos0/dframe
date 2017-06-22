<?php
namespace Dos0\Framework;

use Dos0\Framework\DI\DIInjector;
use Dos0\Framework\Exception\ControllerIsNotFoundException;
use Dos0\Framework\Exception\MethodOfControllerIsNotFoundException;
use Dos0\Framework\Middleware\Middleware;
use Dos0\Framework\Render\Render;
use Dos0\Framework\Request\Request;
use Dos0\Framework\Response\ResponsePrepare;
use Dos0\Framework\Router\Router;
use Dos0\Framework\Router\Exception\RouteIsNotFoundException;
use Dos0\Framework\Router\Route;

/**
 * Class Application
 * @package Dos0\Framework
 */
class Application
{
    const FRAMEWORK_CONFIG_FILE = __DIR__ . '/config/main.php';

    public function __construct($config = [])
    {
        DIInjector::setConfig(array_merge_recursive(
            require self::FRAMEWORK_CONFIG_FILE, $config));
    }

    public function run()
    {
        /* @var ResponsePrepare $responsePrepare */
        $responsePrepare = DIInjector::get('ResponsePrepare');

        try {
            /* @var Router $router */
            $router = DIInjector::get('Router', ['config' => DIInjector::getConfig()]);

            /* @var Request $request */
            $request = DIInjector::get('Request');

            DIInjector::set(Route::class, $router->getRoute($request));
            $route = DIInjector::get('Route');

            // Middleware
            DIInjector::get('Middleware');

            debug('<hr>');

            // @todo Сделать обработчик для json ответов
            $responsePrepare->setData(
                $this->getControllerResult($route)
            );

        } catch (RouteIsNotFoundException $e) {
            $responsePrepare->setCodeAndData(404,
                $this->getErrorData(404, $e->getMessage())
            );

        } catch (\Exception $e) {
            $responsePrepare->setCodeAndData(500,
                $this->getErrorData(500, $e->getMessage())
            );
        }

        $response = $responsePrepare->make();
        $response->send();
    }

    public function __destruct()
    {
        //@TODO: Close active connections, etc.
    }

    /**
     * Gets result of controller method
     *
     * @param Route $route
     * @return mixed
     * @throws ControllerIsNotFoundException
     * @throws MethodOfControllerIsNotFoundException
     */
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

    /**
     * Get data error string
     *
     * @param int $code
     * @param string $errorMessage
     * @return string
     */
    private function getErrorData(int $code, string $errorMessage): string
    {
        if (DIInjector::get('Request')->isJson()) {
            $errorData =
                '{
                    "result": "error", 
                    "code": ' . $code . ', 
                    "message": "' . $errorMessage . '"
                }';
        } else {
            /* @var Render $render */
            $render = DIInjector::get(
                'Render', ['viewPaths' => DIInjector::getConfig()['render']]);

            $errorData = $render->render($code . '.html.php', ['message' => $errorMessage]);
        }

        return $errorData;
    }
}




function debug($param)
{
    echo "<pre>";
    print_r($param);
    echo "</pre>";
}