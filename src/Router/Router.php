<?php

namespace Dos0\Framework\Router;

use Dos0\Framework\Router\Exception\ActionOfRouteNotExistsException;
use Dos0\Framework\Router\Exception\MarkerOfPatternIsNotCorrectedException;
use Dos0\Framework\Router\Exception\RouteIsNotFoundException;
use Dos0\Framework\Router\Exception\RouteKeyNotExistsException;
use Dos0\Framework\Router\Exception\ParamsOfVariableNotExistsException;
use Dos0\Framework\Router\Exception\PatternOfRouteNotExistsException;
use Dos0\Framework\Router\Exception\ParamsOfVariableIsNotMatchedException;
use Dos0\Framework\Request\Request;


// @todo Проверить правильную обработку путей роутера в зависимости от типа запроса (Post, get, etc.)
class Router
{
    /**
     * All routes of config
     *
     * @var array
     */
    private $routes = [];

    /**
     * Router constructor.
     *
     * @param array $config
     * @throws ActionOfRouteNotExistsException
     * @throws PatternOfRouteNotExistsException
     * @throws RouteKeyNotExistsException
     */
    public function __construct(array $config)
    {
        foreach ($config as $key => $value) {

            if (!array_key_exists($key, $config)) {
                throw new RouteKeyNotExistsException("Route Key '$key' Not Exist");
            }
            if (empty($value['pattern'])) {
                throw new PatternOfRouteNotExistsException("Pattern Of Route '{$value['pattern']}' Not Exists");
            }
            if (empty($value['action']) || strpos($value['action'], '@') === false) {
                throw new ActionOfRouteNotExistsException("Action Of Route '{$value['pattern']}' IS Not Corrected");
            }

            $variables = $this->getVariables($value['pattern']);

            $this->routes[$key] = [
                'method' => isset($value['method']) ? $value['method'] : 'GET',
                'controllerName' => $this->getControllerName($value),
                'controllerMethod' => $this->getControllerMethod($value),

                'variables' => $variables,
                'regexpPattern' => $this->getRegexpPattern($value['pattern'], $variables),

                'pattern' => $value['pattern'],
            ];
        }

    }

    /**
     * Gets Route from Request
     *
     * @param Request $request
     * @return Route
     * @throws RouteIsNotFoundException
     */
    public function getRoute(Request $request): Route
    {
        foreach ($this->routes as $key => $route) {

            if (preg_match('/' . $route['regexpPattern'] . '/U', $request->getUri(), $matches)) {

                $params = [];

                if (count($matches) > 2) {
                    array_shift($matches);

                    foreach ($route['variables'] as $variable) {

                        $params[$variable['varName']] = array_shift($matches);
                    }
                }

                $routeResult = new Route();
                $routeResult->setName($key);
                $routeResult->setController($route['controllerName']);
                $routeResult->setMethod($route['controllerMethod']);

                $routeResult->setParams($params);
            }
        }

        if (empty($routeResult)) {
            throw new RouteIsNotFoundException("Route for '{$request->getUri()}' Is Not Found");
        }

        return $routeResult;
    }

    /**
     * Gets variable from config from "pattern"
     * Pattern template:
     * "pattern" => "/good/{id:\d+}/params/{params:\S+}"
     *
     * Returns arrays of variables
     * ['id' => [
     *  ['marker'] => '{id:\d+}',
     *  ['varName'] => 'id',
     *  ['regexp'] => '\d+'
     * ],
     * ...]
     *
     * @param string $pattern
     * @return array
     * @throws MarkerOfPatternIsNotCorrectedException
     */
    private function getVariables(string $pattern): array
    {
        $variables = [];

        if (preg_match_all('/{([^}]+)}/i', $pattern, $matches, PREG_SET_ORDER)) {

            foreach ($matches as $marker) {

                if (strpos($marker[0], ':') === false) {
                    throw new MarkerOfPatternIsNotCorrectedException("Marker '{$marker[0]}' Of Pattern '{$pattern}' Is Not Corrected");
                }

                $markerMap = explode(':', $marker[1], 2);

                $variables[$markerMap[0]] = [
                    'marker' => $marker[0],
                    'varName' => $markerMap[0],
                    'regexp' => $markerMap[1]
                ];
            }
        }

        return $variables;
    }

    /**
     * Gets full regexp expression from pattern
     *
     * @param string $pattern
     * @param array $variables
     * @return string
     */
    private function getRegexpPattern(string $pattern, array $variables): string
    {
        $fullRegexp = str_replace("/", "\/", $pattern);

        foreach ($variables as $variableName => $variableMap) {

            $fullRegexp = str_replace($variableMap['marker'], '('.$variableMap['regexp']. ')', $fullRegexp);
        }
        $fullRegexp = "^" . $fullRegexp . "$";

        return $fullRegexp;
    }

    /**
     * Gets controller name
     *
     * @param array $config_route
     * @return string
     */
    private function getControllerName(array $config_route): string
    {
        return explode("@", $config_route["action"], 2)[0];
    }

    /**
     * Gets controller method name
     *
     * @param array $config_route
     * @return string
     */
    private function getControllerMethod(array $config_route): string
    {
        return explode("@", $config_route["action"], 2)[1];
    }


    /**
     * Builds and gets link from config pattern, binds variables
     *
     * @param string $key
     * @param array $params
     * @return string
     * @throws ParamsOfVariableIsNotMatchedException
     * @throws ParamsOfVariableNotExistsException
     * @throws PatternOfRouteNotExistsException
     * @throws RouteKeyNotExistsException
     */
    public function getLink(string $key, array $params = []): string
    {
        if (!array_key_exists($key, $this->routes)) {
            throw new RouteKeyNotExistsException("Route Key '$key' Not Exist");
        }
        $route = $this->routes[$key];

        if (empty($route['pattern'])) {
            throw new PatternOfRouteNotExistsException("Pattern Of Route '{$route['pattern']}' Not Exists");
        }
        $link = $route['pattern'];

        if (!empty($route['variables'])) {

            foreach ($route['variables'] as $variable) {

                if (empty($params[$variable['varName']])) {
                    throw new ParamsOfVariableNotExistsException(
                        "Params '{$params[$variable['varName']]}' Of Variable Not Exist");
                }
                if (!preg_match('/' . $variable['regexp'] . '/i', $params[$variable['varName']])) {
                    throw new ParamsOfVariableIsNotMatchedException(
                        "Params '{$params[$variable['varName']]}' Of Variable Is Not Matched '{$variable['regexp']}'");
                }

                $link = str_replace($variable['marker'], $params[$variable['varName']], $link);
            }
        }

        return $link;
    }



}