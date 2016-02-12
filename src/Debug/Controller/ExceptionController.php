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
namespace Veto\Debug\Controller;

use Veto\HTTP\Request;
use Veto\HTTP\Response;
use Veto\MVC\AbstractController;

/**
 * ExceptionController
 *
 * This is the default exception controller that will be called for any exceptions
 * from user code that make it to the kernel.
 *
 * You can override this by specifying a different class to be used for the
 * controller._exception_handler service.
 *
 * @since 0.1
 */
class ExceptionController extends AbstractController
{
    public function handleExceptionAction(Request $request, \Exception $exception)
    {
        // The templates for showing exceptions are outside of the normal application
        // template path. It is therefore necessary to specify the path here.
        $this->get('templating')->addPath(
            __DIR__ . '/../Resources/Exception'
        );

        // Render the template
        $response = $this->get('templating')->render('TextHtml.twig', array(
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
            'message' => $exception->getMessage(),
            'type' => get_class($exception)
        ));

        return new Response($response);
    }
}
