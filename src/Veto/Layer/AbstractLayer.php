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
namespace Veto\Layer;
use Veto\HTTP\Request;
use Veto\HTTP\Response;

/**
 * AbstractLayer
 *
 * @since 0.1
 */
abstract class AbstractLayer
{
    protected $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function in(Request $request)
    {
        return $request;
    }

    public function out(Response $response)
    {
        return $response;
    }
}
