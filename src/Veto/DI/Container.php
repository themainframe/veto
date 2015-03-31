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
     * Services defined for this container.
     *
     * @var Definition[]
     */
    private $definitions;

    /**
     * Cached instances of services in this container.
     *
     * @var object[]
     */
    private $instances;

    /**
     * Define a service within the container, optionally providing a pre-existing instance of the service.
     *
     * @param Definition $definition
     * @param mixed|null $instance
     */
    public function define(Definition $definition, $instance = null)
    {
        $this->definitions[$definition->getName()] = $definition;

        if (!is_null($instance)) {
            $this->instances[$definition->getName()] = $instance;
        }
    }

    /**
     * Helper method to define a service by name and instance.
     *
     * @param string $serviceName
     * @param mixed $instance
     */
    public function defineInstance($serviceName, $instance)
    {
        $definition = new Definition();
        $definition->setName($serviceName);
        $definition->setClassName(get_class($instance));

        $this->define($definition, $instance);
    }

    /**
     * Undefine a service by name.
     *
     * @param $serviceName
     */
    public function undefine($serviceName)
    {
        if (isset($this->definitions[$serviceName])) {
            unset($this->definitions[$serviceName]);
        }
    }

    /**
     * Retrieve a service from the container.
     *
     * @param $serviceName
     * @return mixed
     * @throws \RuntimeException
     */
    public function get($serviceName)
    {
        if (!$this->isDefined($serviceName)) {
            throw new \RuntimeException('The service name "' . $serviceName . '" is not defined.');
        }

        // Retrieve the definition
        $definition = $this->definitions[$serviceName];

        // If the service isn't One Shot and has already been built, return the instance
        if (!$definition->isOneShot() && $this->isInstantiated($serviceName)) {
            return $this->instances[$serviceName];
        }

        // Obtain the instance
        $instance = $this->buildInstance($definition);

        // Cache the instance if it isn't One Shot
        if (!$definition->isOneShot()) {
            $this->instances[$serviceName] = $instance;
        }

        return $instance;
    }

    /**
     * Build and return a new instance of a service from a Definition instance.
     *
     * @param Definition $definition
     * @return object
     */
    private function buildInstance(Definition $definition)
    {
        // If any parameters are defined, resolve them
        $definedParameters = $definition->getParameters();
        $parameters = array();

        if (is_array($definedParameters) && count($definedParameters) > 0) {
            $parameters = $this->resolveParameters($definedParameters);
        }

        $reflectionClass = new \ReflectionClass($definition->getClassName());
        $instance = $reflectionClass->newInstanceArgs($parameters);

        // Process any calls that have been defined
        if (is_array($definition->getCalls())) {
            foreach ($definition->getCalls() as $method => $arguments) {
                if ($reflectionClass->hasMethod($method)) {
                    // Resolve the arguments as parameters
                    $resolvedArguments = $this->resolveParameters($arguments);
                    call_user_func_array(array($instance, $method), $resolvedArguments);
                }
            }
        }

        // If the service is a container accessor, provide this container to it
        if ($instance instanceof AbstractContainerAccessor) {
            $instance->setContainer($this);
        }

        return $instance;
    }

    /**
     * Check if $serviceName is defined inside this container.
     *
     * @param $serviceName
     * @return bool
     */
    public function isDefined($serviceName)
    {
        return array_key_exists($serviceName, $this->definitions);
    }

    /**
     * Check if $serviceName is defined and instantiated inside this container.
     *
     * @param $serviceName
     * @return bool
     */
    public function isInstantiated($serviceName)
    {
        return $this->isDefined($serviceName) && array_key_exists($serviceName, $this->instances);
    }

    /**
     * @return Definition[]
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * Resolve an array of parameter names into their actual instances.
     *
     * @param array $parameters
     * @return array
     */
    private function resolveParameters(array $parameters)
    {
        $resolvedParameters = array();

        foreach ($parameters as $parameter) {

            // Does this parameter look like a reference to a service (@servicename) ?
            if (is_string($parameter) && strlen($parameter) > 0 && $parameter[0] == '@') {
                $bareParameterName = substr($parameter, 1);
                $resolvedParameters[] = $this->get($bareParameterName);
            } else {
                $resolvedParameters[] = $parameter;
            }
        }

        return $resolvedParameters;
    }

}
