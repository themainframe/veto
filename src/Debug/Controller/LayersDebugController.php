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

use Veto\Http\Response;
use Veto\MVC\AbstractController;

/**
 * ListenersDebugController
 *
 * Controller to render a debug dump of the registered layers in the application.
 */
class LayersDebugController extends AbstractController
{
    /**
     * @return Response
     */
    public function layersDebug()
    {
        // The templates for showing exceptions are outside of the normal application
        // template path. It is therefore necessary to specify the path here.
        $this->get('php_template_engine')->addPath(
            __DIR__ . '/../Resources/LayersDebug'
        );

        // Render the template
        $response = $this->get('php_template_engine')->render('List.html.php', array(
            'layerPriorityGroups' => $this->container->get('chain')->getLayers()
        ));

        return new Response($response);
    }
}
