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
 * Definition
 *
 * Data carrier class that defines the properties of a service.
 */
class Definition
{
    /**
     * The name of the service.
     *
     * @var string
     */
    private $name;

    /**
     * The FQCN of the service.
     *
     * @var string
     */
    private $className;

    /**
     * Whether or not the service is One Shot.
     *
     * @var boolean
     */
    private $isOneShot;

    /**
     * Parameters to be passed when the service is constructed.
     *
     * @var array
     */
    private $parameters;

    /**
     * Methods to call after the service is constructed.
     *
     * @var array
     */
    private $calls;

    /**
     * Initialise a definition from a name and an array of configuration.
     *
     * @param string $serviceName
     * @param array $configuration
     * @return self
     */
    public static function initWithArray($serviceName, array $configuration)
    {
        $definition = new self;
        $definition->setName($serviceName);

        foreach ($configuration as $key => $value) {
            switch ($key) {

                case 'class':
                    $definition->setClassName($value);
                    break;

                case 'one_shot':
                    $definition->setIsOneShot((boolval($value)));
                    break;

                case 'parameters':
                    $definition->setParameters($value);
                    break;

                case 'calls':
                    $definition->setCalls($value);
                    break;
            }
        }

        return $definition;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Definition
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string $className
     * @return Definition
     */
    public function setClassName($className)
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOneShot()
    {
        return $this->isOneShot;
    }

    /**
     * @param boolean $isOneShot
     * @return Definition
     */
    public function setIsOneShot($isOneShot)
    {
        $this->isOneShot = $isOneShot;
        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return Definition
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @return array
     */
    public function getCalls()
    {
        return $this->calls;
    }

    /**
     * @param array $calls
     * @return Definition
     */
    public function setCalls($calls)
    {
        $this->calls = $calls;
        return $this;
    }
}
