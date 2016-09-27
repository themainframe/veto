<?php
/**
 * Veto.
 * PHP Microframework.
 *
 * @author Damien Walsh <me@damow.net>
 * @copyright Damien Walsh 2013-2014
 * @version 0.3
 * @package veto
 */
namespace Veto\Controller\Controller;

use Veto\Http\Request;
use Veto\Http\Response;
use Veto\Controller\AbstractController;

/**
 * ExceptionController
 *
 * This is the default exception controller that will be called for any exceptions
 * from user code that make it to the kernel.
 *
 * You can override this by specifying a different class to be used for the
 * controller._exception_handler service.
 *
 * @since 0.3.0
 */
class ExceptionController extends AbstractController
{
    public function handleExceptionAction(Request $request, \Exception $exception)
    {
        $response = 'Sorry, something went wrong.';

        if (404 === $exception->getCode()) {
            $response = 'The requested resource could not be found.';
        }

        return new Response($response, $exception->getCode());
    }
}
