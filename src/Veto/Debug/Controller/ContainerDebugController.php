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
 * ContainerDebugController
 *
 * Controller to render a debug dump of the registered services in the Veto Service Container.
 */
class ContainerDebugController extends AbstractController
{
    public function containerDebug(Request $request)
    {
        // The templates for showing exceptions are outside of the normal application
        // template path. It is therefore necessary to specify the path here.

        // TODO: Improve this mechanic.
        $this->get('templating')->addPath(
            $this->get('app')->path . '/Veto/Debug/Resources/ContainerDebug'
        );

        // Render the template
        $response = $this->get('templating')->render('List.twig', array(
            'services' => $this->container->getDefinitions()
        ));

        return new Response($response);
    }
}
