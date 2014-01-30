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

use Veto\HTTP\Response;

/**
 * ExceptionController
 * Tags requests for the kernel to dispatch to controllers.
 *
 * @since 0.1
 */
class ExceptionController extends AbstractController
{
    public function showExceptionAction($exception)
    {
        // Is twig available?
        if ($this->container->has('twig')) {



        }

        return new Response();
    }
}
