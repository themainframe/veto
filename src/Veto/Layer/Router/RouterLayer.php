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

use Veto\App;
use Veto\Collection\Bag;
use Veto\DI\AbstractContainerAccessor;
use Veto\HTTP\Request;
use Veto\Layer\InboundLayerInterface;
use Veto\Exception\ConfigurationException;

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

    public function __construct(App $app)
    {
        $this->routes = new Bag();

        if (!$app->config->get('routes') ||
            !is_array($app->config['routes'])) {
            throw ConfigurationException::missingKey('routes');
        }

        foreach ($app->config['routes'] as $routeName => $route) {
            $this->addRoute(
                $routeName,
                $route['url'],
                isset($route['methods']) ? $route['methods'] : array(),
                $route['controller'],
                $route['action']
            );
        }
    }

    /**
     * Add a route with an arbitrary method to the router.
     */
    public function addRoute($name, $pattern, $methods = array('GET'), $controller, $action)
    {
        $newRoute = new Route($name, $pattern, $methods, $controller, $action);
        $this->routes->add($name, $newRoute);
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

        foreach ($this->routes as $route) {

            $placeholders = $route->matches($request);
            if ($placeholders !== false) {

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
            throw new \Exception(
                'No route defined for ' . $request->getMethod() . ' ' .
                $request->getUri()->getPath(),
                404
            );
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
    public function generateUrl($routeName, array $parameters)
    {
        $route = $this->routes->get($routeName);

        if (!($route instanceof Route)) {
            throw new \Exception(
                'Cannot generate a URL for non-existent route ' . $routeName,
                500
            );
        }

        return $route->generateUrl($parameters);
    }
}
