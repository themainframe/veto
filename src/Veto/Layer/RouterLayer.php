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
namespace Veto\Layer;

use Veto\HTTP\Request;

/**
 * RouterLayer
 * Tags requests for the kernel to dispatch to controllers.
 *
 * @since 0.1
 */
class RouterLayer extends AbstractLayer
{
    public function in(Request $request)
    {
        // Get the App
        $app = $this->container->get('app');

        // Check we have routes
        if (!isset($app->config['routes']) || !count($app->config['routes'])) {
            throw new \Exception('No routes are defined.');
        }

        // Match the first route
        $uri = $request->getUri();
        $tagged = false;

        foreach ($app->config['routes'] as $routeName => $route) {

            if (!isset($route['url'])) {
                // Skip routes with no URL
                continue;
            }

            if (preg_match('@^' . quotemeta($route['url']) . '$@', $uri, $matches)) {

                // Tag the request with the specified controller
                $request->parameters->add('_controller', array(
                    'class' => $route['controller'],
                    'method' => $route['action']
                ));

                $tagged = true;

                // Don't attempt to match any more
                break;
            }

        }

        // If no suitable route was found...
        if (!$tagged) {
            throw new \Exception('No route defined for URL ' . $uri);
        }

        return $request;
    }
}
