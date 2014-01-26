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
namespace Veto\MVC;

/**
 * Controller
 * Tags requests for the kernel to dispatch to controllers.
 *
 * @since 0.1
 */
class AbstractController
{
    /**
     * @var \Veto\DI\Container
     */
    protected $container;

    /**
     * @param \Veto\DI\Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @return \Veto\DI\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function get($serviceName)
    {
        return $this->container->get($serviceName);
    }
}
