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

use Veto\DI\AbstractContainerAccessor;
use Veto\DI\Container;
use Veto\HTTP\Request;
use Veto\HTTP\Response;
use Veto\Layer\AbstractLayer;

/**
 * App
 *
 * Represents the kernel of a web application.
 *
 * @since 0.1
 */
class App extends AbstractContainerAccessor
{
    /**
     * @var string
     */
    public $name = 'Veto';

    /**
     * @var string
     */
    public $version = "0.1.1";

    /**
     * @var AbstractLayer[]
     */
    private $layers;

    /**
     * @var array
     */
    public $config;

    /**
     * Create a new application instance.
     *
     * @param string $configPath The path to the JSON configuration file.
     */
    public function __construct($configPath = '../config/app.json')
    {
        // Read configuration information
        $configJSON = file_get_contents($configPath);
        $this->config = json_decode($configJSON, true);

        // Initialise service container
        $this->container = new Container;

        // Register the kernel
        $this->container->registerInstance('app', $this);

        // Register services
        $this->registerServices(
            isset($this->config['services']) ? $this->config['services'] : array()
        );

        // Initialise middleware
        $this->registerLayers(
            isset($this->config['layers']) ? $this->config['layers'] : array()
        );
    }

    /**
     * Recursively register an array of services as presented in the configuration JSON.
     *
     * @param array $services The services to register
     * @param string $namespace The namespace under which to register services
     */
    private function registerServices(array $services, $namespace = '')
    {
        foreach ($services as $name => $element) {

            if (isset($element['class'])) {

                // This is a service definition
                $this->container->register(
                    $namespace . ($namespace ? '.' : '') . $name,
                    $element['class'],
                    isset($element['parameters']) ? $element['parameters'] : array(),
                    isset($element['persistent']) ? $element['persistent'] : false
                );

            } else {

                // This is an array of services in a namespace
                $this->registerServices(
                    $element,
                    $namespace . ($namespace ? '.' : '') . $name
                );

            }
        }
    }

    /**
     * Register an array of layers as presented in the configuration JSON.
     *
     * @param array $layers The layers to register
     */
    private function registerLayers(array $layers)
    {
        foreach ($layers as $layerName => $layer)
        {
            $newLayer = $this->container->get($layer);
            $newLayer->setContainer($this->container);
            $this->layers[$layerName] = $newLayer;
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
        foreach ($this->layers as $layer) {
            $request = $layer->in($request);
        }

        // Dispatch the request
        $response = $this->dispatch($request);

        // Pass through layers back outwards
        $reversedLayers = array_reverse($this->layers);
        foreach ($reversedLayers as $layer) {
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

        // Prepare to run the action method
        $actionMethod = new \ReflectionMethod($controller, $controllerSpec['method']);
        $parameters = $actionMethod->getParameters();
        $passedArgs = array();

        foreach ($parameters as $parameter) {

            $hintedClass = $parameter->getClass();
            $parameterName = $parameter->getName();

            if ($hintedClass) {
                $hintedClass = $hintedClass->getName();
            }

            // Special case - should the Request object be passed here?
            if ($parameterName == 'request' && $hintedClass == 'Veto\HTTP\Request') {
                $passedArgs[] = $request;
            }

            // Should a request parameter be passed here?
            if ($request->parameters->has($parameterName)) {
                $passedArgs[] = $request->parameters->get($parameterName);
            }
        }

        // Get the response by calling the controller
        $response = $actionMethod->invokeArgs($controller, $passedArgs);

        return $response;
    }
}
