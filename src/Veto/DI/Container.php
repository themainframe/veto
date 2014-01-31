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
    private $registeredClasses = array();

    /**
     * Register a service in the container.
     *
     * @param string $alias The name for the service.
     * @param string $className The absolute class name.
     * @param array $params Optionally any parameters to pass to the constructor.
     * @param bool $persist Optionally return the same instance every time the service is requested.
     */
    public function register($alias, $className, $params = array(), $persist = false)
    {
        $this->registeredClasses[$alias] = array(
            'className' => $className,
            'parameters' => $params,
            'isPersistent' => $persist
        );
    }

    /**
     * Register an existing instace in the container.
     *
     * @param string $alias The name for the service.
     * @param mixed $instance The existing object.
     */
    public function registerInstance($alias, $instance)
    {
        $this->registeredClasses[$alias] = array(
            'className' => get_class($instance),
            'isPersistent' => true,
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

            // Use the persisted instance?
            if ($definition['isPersistent'] && isset($definition['instance'])) {
                return $definition['instance'];
            }

            $parameterAliases = $definition['parameters'];
            $className = $definition['className'];
            $parameters = array();

            foreach ($parameterAliases as $parameterAlias) {
                if (is_string($parameterAlias) && $parameterAlias[0] == '@') {
                    $parameterAlias = substr($parameterAlias, 1);
                    $parameters[] = $this->get($parameterAlias);
                } else {
                    $parameters[] = $parameterAlias;
                }
            }

            $reflectionClass = new \ReflectionClass($className);
            $instance = $reflectionClass->newInstanceArgs($parameters);

            // Persist?
            if ($definition['isPersistent']) {
                $this->registeredClasses[$alias]['instance'] =
                    $instance;
            }

            return $instance;

        } else {
            throw new \Exception('Unknown DI alias ' . $alias);
        }
    }
}
