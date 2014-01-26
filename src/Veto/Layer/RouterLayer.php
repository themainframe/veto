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

/**
 * RouterLayer
 * Tags requests for the kernel to dispatch to controllers.
 *
 * @since 0.1
 */
class RouterLayer extends AbstractLayer
{
    public function in(Request $request)
    {
        // TODO: For now, tag the request with a dummy controller
        $request->parameters->add('_controller', array(
            'class' => 'controllers.helloworld',
            'method' => 'sayHelloAction'
        ));

        return $request;
    }
}
