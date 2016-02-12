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

use Veto\DI\AbstractContainerAccessor;
use Veto\HTTP\Response;

/**
 * AbstractController
 * Helper class to provide common utilities to controllers.
 *
 * @since 0.1
 */
abstract class AbstractController extends AbstractContainerAccessor
{
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

    public function render($templateName, array $parameters = array())
    {
        return new Response(
            $this->get('templating')->render($templateName, $parameters)
        );
    }
}
