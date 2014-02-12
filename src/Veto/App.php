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

use Veto\Configuration\Hive;
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
     * @var Tree
     */
    public $parameters;

    /**
     * @var Hive
     */
    public $config = array();

    /**
     * The base path of the application.
     * Resolves to the directory that contains the kernel class file (App.php).
     *
     * @var string
     */
    public $path;

    /**
     * Create a new application instance.
     *
     * @param string $configPath The path to the JSON configuration file.
     */
    public function __construct($configPath)
    {
        // Set the base directory
        $this->path = dirname(__DIR__);

        // Create the configuration hive and load the base config
        $this->config = new Hive;

        // Load the base configuration
        $this->config->loadJson($this->path . '/../config/base.json');

        // Read configuration information
        $this->config->loadJson($configPath);

        // Initialise service container
        $this->container = new Container;

        // Register the kernel & configuration hive
        $this->container->registerInstance('config', $this->config);
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
                    isset($element['one_shot']) ? $element['one_shot'] : false
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
        try {

            // Pass through layers inwards
            foreach ($this->layers as $layer) {

                if ($request->getSkipAll()) {
                    break;
                }

                $request = $layer->in($request);
            }

            // Dispatch the request
            $response = $this->dispatch($request);

            // Pass through layers back outwards
            $reversedLayers = array_reverse($this->layers);
            foreach ($reversedLayers as $layer) {

                if ($response->getSkipAll()) {
                    break;
                }

                $response = $layer->out($response);
            }

        } catch(\Exception $exception) {

            // Invoke the exception controller action method
            $exceptionHandler = $this->container->get('controller._exception_handler');
            $exceptionHandler->setContainer($this->container);
            $response = $exceptionHandler->handleExceptionAction($request, $exception);
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

        if (!method_exists($controller, $controllerSpec['method'])) {
            throw new \Exception(
                'The controller action "' . $controllerSpec['method'] .
                '" does not exist for controller "' .
                $controllerSpec['class'] . '".'
            );
        }

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

        $response = $actionMethod->invokeArgs($controller, $passedArgs);

        return $response;
    }
}
