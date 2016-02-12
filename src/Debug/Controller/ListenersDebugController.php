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

use Veto\HTTP\Response;
use Veto\MVC\AbstractController;

/**
 * ListenersDebugController
 *
 * Controller to render a debug dump of the registered listeners on the event dispatcher service.
 */
class ListenersDebugController extends AbstractController
{
    /**
     * @return Response
     */
    public function listenersDebug()
    {
        // The templates for showing exceptions are outside of the normal application
        // template path. It is therefore necessary to specify the path here.

        // TODO: Improve this mechanic.
        $this->get('templating')->addPath(
            __DIR__ . '/../Resources/ListenersDebug'
        );

        // Render the template
        $response = $this->get('templating')->render('List.twig', array(
            'eventNames' => $this->container->get('dispatcher')->getListeners()
        ));

        return new Response($response);
    }
}
