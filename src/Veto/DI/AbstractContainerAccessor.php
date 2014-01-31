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
 * Represents an object that can access a service container.
 *
 * @since 0.1
 */
abstract class AbstractContainerAccessor
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Set the service container.
     *
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
