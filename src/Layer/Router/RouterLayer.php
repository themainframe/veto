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
namespace Veto\Layer\Router;

use Veto\Collection\Bag;
use Veto\Layer\Router\Exception\RouterException;
use Veto\Configuration\Hive;
use Veto\DI\AbstractContainerAccessor;
use Veto\Event\Dispatcher;
use Veto\HTTP\Request;
use Veto\Layer\InboundLayerInterface;
use Veto\Configuration\Exception\ConfigurationException;

/**
 * RouterLayer
 * Tags requests for the kernel to dispatch to controllers.
 *
 * @since 0.1
 */
class RouterLayer extends AbstractContainerAccessor implements InboundLayerInterface
{
    /**
     * The routes managed by this Router.
     *
     * @var \Veto\Collection\Bag
     */
    protected $routes;

    /**
     * The event dispatcher.
     *
     * @var \Veto\Event\Dispatcher
     */
    protected $dispatcher;

    /**
     * Initialise the router, optionally from configuration
     *
     * @param Hive $config
     * @param Dispatcher $dispatcher
     * @throws ConfigurationException
     */
    public function __construct(Hive $config = null, Dispatcher $dispatcher = null)
    {
        $this->routes = new Bag();
        $this->dispatcher = $dispatcher;

        if (!is_null($config) && $config->get('routes') && is_array($config['routes'])) {
            foreach ($config['routes'] as $routeName => $route) {
                $this->addRoute(
                    $routeName,
                    $route['pattern'],
                    isset($route['rules']) ? $route['rules'] : array(),
                    isset($route['methods']) ? $route['methods'] : array(),
                    $route['controller'],
                    $route['action']
                );
            }
        }
    }

    /**
     * Add a route with an arbitrary method to the router.
     *
     * @param $name
     * @param $pattern
     * @param string[] $rules
     * @param string[] $methods
     * @param $controller
     * @param $action
     */
    public function addRoute($name, $pattern, array $rules, $methods = array('GET'), $controller, $action)
    {
        $this->routes->add($name, new Route($name, $pattern, $rules, $methods, $controller, $action));
    }

    /**
     * Tag a request $request with a controller so that the kernel (Veto\App)
     * can dispatch it to a controller.
     *
     * @param Request $request
     * @return Request
     * @throws \Exception
     */
    public function in(Request $request)
    {
        $tagged = false;

        /**
         * @var Route $route
         */
        foreach ($this->routes as $route) {

            $placeholders = $route->matches($request);
            if ($placeholders !== false) {

                // Dispatch a matched event
                if (!is_null($this->dispatcher)) {
                    $this->dispatcher->dispatch(RouterEvent::ROUTE_MATCHED, new RouterEvent($route, $this));
                }

                // Add the matched route's parameters to the request
                $request = $request->withParameter('_controller', array(
                    'class' => $route->getController(),
                    'method' => $route->getAction()
                ));

                // Add any matched route placeholders to the request parameters
                foreach ($placeholders as $placeholderKey => $placeholderValue) {
                    $request = $request->withParameter($placeholderKey, $placeholderValue);
                }

                $tagged = true;
                break;
            }
        }

        // If no suitable route was found...
        if (!$tagged) {
            throw RouterException::noRouteExists($request->getMethod(), $request->getUri()->getPath());
        }

        return $request;
    }

    /**
     * Generate a URL from a route name and paramters.
     *
     * @param string $routeName
     * @param array $parameters
     * @return string
     * @throws \Exception
     */
    public function generateUrl($routeName, array $parameters = array())
    {
        $route = $this->routes->get($routeName);

        if (!($route instanceof Route)) {
            throw RouterException::nonExistentRoute($routeName);
        }

        return $route->generateUrl($parameters);
    }
}
