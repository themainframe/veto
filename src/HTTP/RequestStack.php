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
namespace Veto\HTTP;

use Psr\Http\Message\RequestInterface;

/**
 * RequestStack - a stack of requests being handled by the application.
 */
class RequestStack
{
    /**
     * @var RequestInterface[]
     */
    private $stack = array();

    /**
     * @param RequestInterface $request
     */
    public function push(RequestInterface $request)
    {
        array_push($this->stack, $request);
    }

    /**
     * @return RequestInterface
     */
    public function pop()
    {
        array_pop($this->stack);
    }

    /**
     * Check if this request is the master request.
     * 
     * @return bool
     */
    public function isMasterRequest()
    {
        return count($this->stack) === 1;
    }

    /**
     * Get the master request, or null if no requests are being handled.
     * 
     * @return null|RequestInterface
     */
    public function getMasterRequest()
    {
        return count($this->stack) > 0 ? $this->stack[0] : null;
    }
}
