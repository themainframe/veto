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
namespace Veto;

use Veto\HTTP\Request;
use Veto\HTTP\Response;
use Veto\Layer\CallbackLayer;
use Veto\Layer\LOLAppenderLayer;
use Veto\Layer\RouterLayer;

/**
 * App
 *
 * Represents the kernel of a web application.
 *
 * @since 0.1
 */
class App
{
    /**
     * @var AbstractLayer[]
     */
    private $layers;

    public function __construct()
    {
        $this->layers = array();
        $this->layers[] = new RouterLayer();
        $this->layers[] = new CallbackLayer(null, function(Response $response) {
            return $response->setContent(
                $response->getContent() . ' LOL!'
            );
        });
    }

    /**
     * Handle the request using the defined layer chain.
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        // Pass through layers inwards
        foreach($this->layers as $layer) {
            $request = $layer->in($request);
        }

        // Dispatch the request
        $response = $this->dispatch($request);

        // Pass through layers back outwards
        $reversedLayers = array_reverse($this->layers);
        foreach($reversedLayers as $layer) {
            $response = $layer->out($response);
        }

        // Output content
        $response->send();

        return $response;
    }

    public function dispatch(Request $request)
    {
        // Get the controller
        $controllerSpec = $request->parameters->get('_controller');
        $controller = new $controllerSpec['class'];
        return call_user_func(array($controller, $controllerSpec['method']), $request);
    }
}
