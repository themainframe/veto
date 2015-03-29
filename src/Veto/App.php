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
     * Handle a request using the defined layer chain.
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        try {

            // TODO: Not keen on this, need to find a way to avoid referencing the chain service, tags?
            $layerChain = $this->container->get('chain');
            $response = $layerChain->processLayers($request);

        } catch(\Exception $exception) {

            // Invoke the exception controller action method
            $exceptionHandler = $this->container->get('controller._exception_handler');
            $exceptionHandler->setContainer($this->container);
            $response = $exceptionHandler->handleExceptionAction($request, $exception);
        }

        return $response;
    }
}
