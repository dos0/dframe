<?php

namespace Dos0\Framework\DI;

use Dos0\Framework\DI\Exception\ServiceClassIsNotExistException;
use Dos0\Framework\DI\Exception\ServiceInjectorDoesNotCreateClassException;
use Dos0\Framework\DI\Exception\ServiceKeyNotFoundException;

/**
 * Class DIInjector
 * @package Dos0\Framework\DI
 */
class DIInjector
{
    /**
     * Full config
     *
     * @var array
     */
    private static $config = [];

    /**
     * Class mapping
     *
     * @var array
     */
    private static $serviceMap = [];

    /**
     * Array of existed services
     *
     * @var array
     */
    private static $services = [];

    /**
     * @param array $config
     */
    public static function setConfig(array $config)
    {
        self::$config = $config;
        self::$serviceMap = $config['serviceMap'];
    }

    /**
     * Gets class from short name.
     *
     * @param string $serviceName
     * @param array $params
     * @return object
     */
    public static function get(string $serviceName, array $params = [])
    {
        foreach (self::$services as $keyServiceName => $service) {
            $shortKeyServiceName = explode('\\', $keyServiceName);
            $shortKeyServiceName = array_pop($shortKeyServiceName);

            if ($serviceName == $shortKeyServiceName) {
                return $service;
            }
        }
        return self::make($serviceName, $params);
    }

    /**
     * Sets service to service array
     *
     * @param string $serviceName
     * @param $instance
     */
    public static function set(string $serviceName, $instance)
    {
        self::$services[$serviceName] = $instance;
    }

    /**
     * Gets config array (for debug)
     *
     * @return array
     */
    public static function getConfig(): array
    {
        return self::$config;
    }

    /**
     * Gets array of services (for debug)
     *
     * @return array
     */
    public static function getServices(): array
    {
        return self::$services;
    }

    /**
     * Makes recursively needed service or object
     *
     * @param string $className
     * @param array $makeParams
     * @return mixed|object
     * @throws ServiceClassIsNotExistException
     * @throws ServiceInjectorDoesNotCreateClassException
     * @throws ServiceKeyNotFoundException
     */
    public static function make(string $className, array $makeParams = [])
    {
        if (array_key_exists($className, self::$services)) {
            return self::$services[$className];
        }
        if (class_exists($className)) {
            return self::$services[$className] = new $className();
        }

        if (!array_key_exists($className, self::$serviceMap)) {
            throw new ServiceKeyNotFoundException('Service Key ' . $className . ' Not Found Exception');
        }
        $className = self::$serviceMap[$className];
        if (!class_exists($className)) {
            throw new ServiceClassIsNotExistException('Service Class ' . $className . ' Is Not Exist Exception');
        }

        $reflectionClass = new \ReflectionClass($className);
        if ($reflectionClass->hasMethod('getInstance')) {
            $classConstructor = $reflectionClass->getMethod('getInstance');
        } else {
            $classConstructor = $reflectionClass->getConstructor();
        };

        $classConstructorParams = $classConstructor->getParameters();

        $paramInstance = [];
        foreach ($classConstructorParams as $key => $param) {
            // Array from default params function
            if ($param->isDefaultValueAvailable()) {
                $paramInstance[$param->name] = $param->getDefaultValue();
            }

            // Make needed class recursively
            if ($param->hasType()) {
                $type = (string)$param->getType();
                if (class_exists($type)) {
                    $paramInstance[$param->name] = DIInjector::make($type);
                }
            }

        }

        // Merge with config params
        $keyClassName = explode('\\', $className);
        $keyClassName = array_pop($keyClassName);
        if (array_key_exists(strtolower($keyClassName), self::$config)) {
            $paramInstance = array_merge(
                $paramInstance, self::$config[strtolower($keyClassName)]);
        }

        // Merge with make function params
        if (!empty($makeParams)) {
            $paramInstance = array_merge($paramInstance, $makeParams);
        }

        // try to get new class instance
        if ($reflectionClass->hasMethod('getInstance')) {
            if (!($instance = call_user_func_array([$className, 'getInstance'], $paramInstance))) {
                throw new ServiceInjectorDoesNotCreateClassException(
                    'Service Injector Does Not Create Class ' . $className . ' Exception');
            }
        } else {
            if (!($instance = $reflectionClass->newInstanceArgs($paramInstance))) {
                throw new ServiceInjectorDoesNotCreateClassException(
                    'Service Injector Does Not Create Class ' . $className . ' Exception');
            }
        }

        self::set($className, $instance);
        return $instance;
    }
}