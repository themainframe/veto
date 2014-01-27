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
    /**
     * Check if the URI matches a defined route pattern.
     *
     * Returns an array containing any route parameters on a successful match.
     * Returns false for failed matches.
     *
     * @param string $uri The URI to test.
     * @param string $pattern The pattern to check against.
     * @return array|bool
     */
    private function match($uri, $pattern)
    {
        // Check if the pattern contains any {...} blocks
        preg_match_all('@{([A-Za-z_]+)}@', $pattern, $placeholders);

        if ($placeholders) {

            // Convert all {...} blocks into regex groups
            $pattern = preg_replace('@{[A-Za-z_]+}@', '(.+)', $pattern);

            // Get the placeholder names
            $placeholders = $placeholders[1];

            // See if the route matches
            if (preg_match('@' . $pattern . '@', $uri, $matches)) {
                // Merge the placeholder names with their URI values
                array_shift($matches);
                return array_combine($placeholders, $matches);
            }
        }

        // Simple matching - check if the URI matches the pattern
        return $uri == $pattern ? array() : false;
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
        // Get the App
        $app = $this->container->get('app');

        // Check we have routes
        if (!isset($app->config['routes']) || !count($app->config['routes'])) {
            throw new \Exception('No routes are defined.');
        }

        // Match the first route
        $uri = $request->getUri();
        $tagged = false;

        foreach ($app->config['routes'] as $route) {

            if (!isset($route['url'])) {
                // Skip routes with no URL
                continue;
            }

            if ($placeholders = $this->match($uri, $route['url'])) {

                // Tag the request with the specified controller
                $request->parameters->add('_controller', array(
                    'class' => $route['controller'],
                    'method' => $route['action']
                ));

                // Add any matched route placeholders to the request parameters
                foreach ($placeholders as $placeholderKey => $placeholderValue) {
                    $request->parameters->add($placeholderKey, $placeholderValue);
                }

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
