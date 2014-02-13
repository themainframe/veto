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
     * The name of the layer within the application, distinct from the class.
     *
     * @var string
     */
    protected $name;

    /**
     * Should this layer be ignored by all requests except those that force it?
     *
     * @var bool
     */
    protected $bypassed = false;

    /**
     * Preprocess a request to be handled or skipped by this layer.
     *
     * @param Request $request
     * @return Request
     */
    public final function preIn(Request $request)
    {
        if (!$this->bypassed || $request->isForced($this->name)) {
            return $this->in($request);
        }

        return $request;
    }

    /**
     * Preprocess a response to be handled or skipped by this layer.
     *
     * @param Response $response
     * @return Response
     */
    public final function preOut(Response $response)
    {
        if (!$this->bypassed || $response->isForced($this->name)) {
            return $this->out($response);
        }

        return $response;
    }

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

    /**
     * Mark this layer as bypassed.
     *
     * @param boolean $bypassed
     */
    public function setBypassed($bypassed = true)
    {
        $this->bypassed = $bypassed;
    }

    /**
     * Get this layer's bypassed status.
     *
     * @return boolean
     */
    public function getBypassed()
    {
        return $this->bypassed;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
