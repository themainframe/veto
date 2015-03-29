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
use Veto\DI\Definition;
use Veto\Exception\ConfigurationException;
use Veto\HTTP\Request;
use Veto\HTTP\Response;
use Veto\Layer\AbstractLayer;
use Veto\Layer\InboundLayerInterface;
use Veto\Layer\OutboundLayerInterface;
use Veto\MVC\DispatcherInterface;

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
     * @var object[]
     */
    private $layers;

    /**
     * @var \Veto\Collection\Bag
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
        $this->config->load($this->path . '/../config/base.yml');

        // Read configuration information
        $this->config->load($configPath);

        // Initialise service container
        $this->container = new Container;

        // Register the kernel & configuration hive
        $this->container->defineInstance('config', $this->config);
        $this->container->defineInstance('app', $this);
        $this->container->defineInstance('container', $this->container);

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
                $definition = Definition::initWithArray($namespace . ($namespace ? '.' : '') . $name, $element);
                $this->container->define($definition);

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
     * @throws ConfigurationException
     * @param array $layers The layers to register
     */
    private function registerLayers(array $layers)
    {
        foreach ($layers as $layerName => $layer)
        {
            if (!array_key_exists('service', $layer)) {
                throw ConfigurationException::missingSubkey('layer', 'service');
            }

            $newLayer = $this->container->get($layer['service']);
            $newLayer->setContainer($this->container);
            $this->layers[$layerName] = $newLayer;
        }
    }


    private function processInboundLayers(Request $request)
    {
        $result = $request;

        // Pass through layers inwards
        foreach ($this->layers as $layer) {

            if (!($layer instanceof InboundLayerInterface)) {
                continue;
            }

            $result = $layer->in($result);

            // If the layer produces a response, no more inbound layers may execute
            if ($result instanceof Response) {
                return $result;
            }

            if (!$result instanceof Request) {
                throw new \RuntimeException(
                    'Each inbound layer of the application pipeline must produce a Request or Response type. ' .
                    'The "' . $layer->getName() . '" layer returned ' . gettype($request) . '.'
                );
            }
        }

        return $result;
    }

    private function processOutboundLayers(Response $response)
    {
        $result = $response;

        foreach ($this->layers as $layer) {

            if (!($layer instanceof OutboundLayerInterface)) {
                continue;
            }

            $result = $layer->out($result);

            if (!$result instanceof Response) {
                throw new \RuntimeException(
                    'Each outbound layer of the application pipeline must produce a Response type. ' .
                    'The "' . $layer->getName() . '" layer returned ' . gettype($response) . '.'
                );
            }
        }

        return $result;
    }

    /**
     * Handle a request using the defined layer chain.
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        try {

            $response = $this->processInboundLayers($request);

            // By the end of the inbound layer list, a response should have been obtained
            if (!$response instanceof Response) {
                throw new \RuntimeException(
                    'At least one inbound layer must produce a Response instance. ' .
                    'The final processed layer returned a "' . gettype($response) . '".'
                );
            }

            // The response should now be processed by the outbound layers
            $response = $this->processOutboundLayers($response);

        } catch(\Exception $exception) {

            // Invoke the exception controller action method
            $exceptionHandler = $this->container->get('controller._exception_handler');
            $exceptionHandler->setContainer($this->container);
            $response = $exceptionHandler->handleExceptionAction($request, $exception);
        }

        return $response;
    }
}
