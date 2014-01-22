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
 * CallbackLayer
 * Executes callbacks.
 *
 * @since 0.1
 */
class CallbackLayer extends AbstractLayer
{
    private $inCallback;
    private $outCallback;

    function __construct($inCallback, $outCallback = null)
    {
        $this->inCallback = $inCallback;
        $this->outCallback = $outCallback;
    }

    public function in(Request $request)
    {
        if(is_callable($this->inCallback)) {
            return call_user_func($this->inCallback, $request);
        } else {
            return $request;
        }
    }

    public function out(Response $response)
    {
        if(is_callable($this->outCallback)) {
            return call_user_func($this->outCallback, $response);
        } else {
            return $response;
        }
    }
}
