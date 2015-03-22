<?php
/**
 * Veto.
 * PHP Microframework.
 *
 * @author Damien Walsh <me@damow.net>
 * @copyright Damien Walsh 2013-2014
 * @version 0.1
 * @package veto
 */
namespace Veto\DI;

/**
 * Container
 *
 * @since 0.1
 */
class Container
{
    /**
     * The underlying associative array of services managed by this container.
     *
     * @var array
     */
    protected $registeredClasses = array();

    /**
     * Register a service in the container.
     *
     * @param string $alias The name for the service.
     * @param string $className The absolute class name.
     * @param array $params Optionally any parameters to pass to the constructor.
     * @param bool $oneShot Optionally return a new instance every time the service is located.
     * @param array $calls Optionally an array of methods (keys) and parameters (values) to call on the instance.
     */
    public function register($alias, $className, $params = array(), $oneShot = true, $calls = array())
    {
        $this->registeredClasses[$alias] = array(
            'className' => $className,
            'parameters' => $params,
            'calls' => $calls,
            'isOneShot' => $oneShot
        );
    }

    /**
     * Register an existing instance in the container.
     *
     * @param string $alias The name for the service.
     * @param mixed $instance The existing object.
     */
    public function registerInstance($alias, $instance)
    {
        $this->registeredClasses[$alias] = array(
            'className' => get_class($instance),
            'isOneShot' => false,
            'instance' => $instance
        );
    }

    /**
     * Locate a service by alias and return an instance of it.
     *
     * @param string $alias The alias to look for.
     * @return object|null
     * @throws \Exception
     */
    public function get($alias)
    {
        if (array_key_exists($alias, $this->registeredClasses)) {

            $definition = $this->registeredClasses[$alias];

            // Should a new instance be returned every time?
            if (!$definition['isOneShot'] && isset($definition['instance'])) {
                return $definition['instance'];
            }

            $parameterAliases = $definition['parameters'];
            $className = $definition['className'];
            $parameters = $this->resolveParameterAliases($parameterAliases);

            $reflectionClass = new \ReflectionClass($className);
            $instance = $reflectionClass->newInstanceArgs($parameters);

            // Persist, or one-shot instance?
            if (!$definition['isOneShot']) {
                $this->registeredClasses[$alias]['instance'] =
                    $instance;
            }

            // Call any required methods, passing parameters for each
            foreach ($definition['calls'] as $methodName => $methodParams) {
                if ($reflectionClass->hasMethod($methodName)) {
                    call_user_func_array(array($instance, $methodName), $methodParams);
                }
            }

            return $instance;

        } else if(substr($alias, -2) === '.*') {

            $matchedServices = $this->getNamespace(substr($alias, 0, -1));
            $services = array();

            foreach($matchedServices as $serviceAlias => $service) {
                $services[] = $this->get($serviceAlias);
            }

            return $services;

        } else {
            throw new \Exception('Unknown DI alias ' . $alias);
        }
    }

    /**
     * Return a list of registered services in this container.
     *
     * @return array
     */
    public function getRegisteredServices()
    {
        return array_map(function($service) {

            if (isset($service['instance'])) {
                unset($service['instance']);
            }

            return $service;

        }, $this->registeredClasses);
    }

    private function getNamespace($namespace)
    {
        // Get all the services under a namespace
        $matches = array();

        foreach ($this->registeredClasses as $alias => $service) {
            if (strpos($alias, $namespace) === 0) {
                $matches[$alias] = $service;
            }
        }

        return $matches;
    }

    private function resolveParameterAliases($parameters)
    {
        foreach ($parameters as & $parameter) {
            if (is_array($parameter)) {
                $parameter = $this->resolveParameterAliases($parameter);
            } else {
                if (is_string($parameter) && strlen($parameter) > 0 && $parameter[0] == '@') {
                    $parameter = substr($parameter, 1);
                    $parameter = $this->get($parameter);
                }
            }
        }

        return $parameters;
    }
}
