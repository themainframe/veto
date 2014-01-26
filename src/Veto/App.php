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

use Veto\DI\Container;
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
     * @var string
     */
    public $name = 'Veto';

    /**
     * @var AbstractLayer[]
     */
    private $layers;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var array
     */
    private $config;

    public function __construct($configPath = '../config/app.json')
    {
        // Read configuration information
        $configJSON = file_get_contents($configPath);
        $this->config = json_decode($configJSON, true);

        // Initialise service container
        $this->container = new Container;
        foreach ($this->config['services'] as $name => $service) {
            $this->container->register(
                $name,
                $service['class'],
                isset($service['parameters']) ? $service['parameters'] : array(),
                isset($service['persistent']) ? $service['persistent'] : false
            );
        }

        // Register the kernel
        $this->container->registerInstance('app.kernel', $this);

        // Initialise middleware
        foreach ($this->config['layers'] as $layer) {
            $newLayer = $this->container->get($layer);
            $this->layers[] = $newLayer;
        }
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
        $controller =  $this->container->get($controllerSpec['class']);
        $controller->setContainer($this->container);
        return call_user_func(array($controller, $controllerSpec['method']), $request);
    }
}
