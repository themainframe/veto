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
use Veto\MVC\AbstractController;
use Veto\Twig\HTTP\TwigResponse;

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
        $response = new TwigResponse('TextHtml.twig', array(
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
            'message' => $exception->getMessage(),
            'type' => get_class($exception)
        ));

        $response->setTemplatePath($this->get('app')->path . '/Veto/Debug/Resources/');

        return $response;
    }
}
