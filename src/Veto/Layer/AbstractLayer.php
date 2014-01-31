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

use Veto\DI\AbstractContainerAccessor;
use Veto\HTTP\Request;
use Veto\HTTP\Response;

/**
 * AbstractLayer
 *
 * @since 0.1
 */
abstract class AbstractLayer extends AbstractContainerAccessor
{
    /**
     * Pass the request through this layer, in towards the controller.
     *
     * @param Request $request
     * @return Request
     */
    public function in(Request $request)
    {
        return $request;
    }

    /**
     * Pass the response through this layer, out away from the controller.
     *
     * @param Response $response
     * @return Response
     */
    public function out(Response $response)
    {
        return $response;
    }
}
