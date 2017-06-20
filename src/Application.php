<?php
namespace Dos0\Framework;

use Dos0\Framework\Exception\ControllerIsNotFoundException;
use Dos0\Framework\Exception\MethodOfControllerIsNotFoundException;
use Dos0\Framework\Render\Render;
use Dos0\Framework\Request\Request;
use Dos0\Framework\Response\ResponsePrepare;
use Dos0\Framework\Router\Exception\RouteIsNotFoundException;
use Dos0\Framework\Router\Route;
use Dos0\Framework\Router\Router;

/**
 * Class Application
 * @package Dos0\Framework
 */
class Application
{

    const FRAMEWORK_CONFIG_FILE = 'config/main.php';

    // @todo config вынести в DI
    private $render;
    private static $config;

    private $request;

    public function __construct($config = [])
    {
        $fwConfig = require self::FRAMEWORK_CONFIG_FILE;

        self::$config = array_merge_recursive($fwConfig, $config);

        $this->render = new Render(self::getConfig()['render']);
        $this->request = new Request();
    }

    public function run()
    {
        $responsePrepare = new ResponsePrepare($this->request);

        try {

            $router = new Router(self::$config['routes']);
            $route = $router->getRoute($this->request);

            // @todo Сделать обработчик для json ответов
            $responsePrepare->setData(
                $this->getControllerResult($route)
            );

        } catch (RouteIsNotFoundException $e) {
            $responsePrepare->setCode(404);
            $responsePrepare->setData(
                $this->getErrorData(404, $e->getMessage())
            );

        } catch (\Exception $e) {
            $responsePrepare->setCode(500);
            $responsePrepare->setData(
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
     * Gets application config
     *
     * @return array
     */
    public static function getConfig(): array
    {
        return self::$config;
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
        if ($this->request->isJson()) {
            $errorData =
                '{
                    "result": "error", 
                    "code": ' . $code . ', 
                    "message": "' . $errorMessage . '"
                }';
        } else {
            $errorData = $this->render->render($code . '.html.php', ['message' => $errorMessage]);
        }

        return $errorData;
    }
}

;