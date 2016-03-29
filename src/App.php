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
use Veto\HTTP\Request;
use Veto\HTTP\Response;
use Veto\Layer\LayerChainBuilder;

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
     * @var bool Whether or not the application is running in Debug mode
     */
    public $debug = false;

    /**
     * Create a new application instance.
     *
     * @param bool $debug A flag indicating whether or not to start the application in Debug mode.
     * @param array $config An associative array of configuration settings for the application.
     */
    public function __construct($debug = false, array $config = array())
    {
        // The debug mode flag
        $this->debug = $debug;
        
        // Load the base configuration
        $baseConfig = new Hive();
        $baseConfig->load(dirname(__DIR__) . '/config/base.yml');

        // Load the debug configuration if this application will start in Debug mode
        if ($this->debug) {
            $baseConfig->load(dirname(__DIR__) . '/config/debug.yml');
        }

        // Merge the actual config onto the base config
        $baseConfig->merge($config);

        // Initialise service container
        $this->container = new Container;

        // Register the kernel & configuration hive
        $this->container->defineInstance('config', $baseConfig);
        $this->container->defineInstance('app', $this);
        $this->container->defineInstance('container', $this->container);

        // Register parameters & services
        $this->registerParameters(isset($baseConfig['parameters']) ? $baseConfig['parameters'] : array());
        $this->registerServices(isset($baseConfig['services']) ? $baseConfig['services'] : array());

        // Set up layers
        $layerChain = LayerChainBuilder::initWithConfigurationAndContainer($baseConfig, $this->container);
        $this->container->defineInstance('chain', $layerChain);
    }

    /**
     * Register an array of parameters with the service container.
     *
     * @param array $parameters The parameters to register
     */
    private function registerParameters(array $parameters)
    {
        foreach ($parameters as $name => $value) {
            $this->container->setParameter($name, $value);
        }
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

        } catch (\Exception $exception) {

            // Invoke the exception controller action method
            $exceptionHandler = $this->container->get('controller._exception_handler');
            $exceptionHandler->setContainer($this->container);
            $response = $exceptionHandler->handleExceptionAction($request, $exception);
        }

        return $response;
    }
}
