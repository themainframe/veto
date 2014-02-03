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

        if ($placeholders && isset($placeholders[1])) {

            // Convert all {...} blocks into regex groups
            $pattern = preg_replace('@{[A-Za-z_]+}@', '([^/]+)', $pattern);

            // Get the placeholder names
            $placeholders = $placeholders[1];

            // See if the route matches
            if (preg_match('@^' . $pattern . '$@', $uri, $matches)) {
                // Merge the placeholder names with their URI values
                array_shift($matches);
                return array_combine($placeholders, $matches);
            }
        }

        // Simple matching - check if the URI matches the pattern
        return $uri == $pattern ? array() : false;
    }

    /**
     * Generate a URL from a route name and parameter set.
     *
     * @param string $routeName The route name to use.
     * @param array $parameters Optionally the parameters to use.
     * @throws \Exception
     * @return string|boolean The URL on success, false on failure.
     */
    public function generateUrl($routeName, array $parameters = array())
    {
        // Get the App
        $app = $this->container->get('app');

        // Check we have routes
        if (!isset($app->config['routes']) || !count($app->config['routes'])) {
            throw new \Exception('No routes are defined.');
        }

        // Get the route
        if (!array_key_exists($routeName, $app->config['routes'])) {
            throw new \Exception('Cannot generate URL for non-existent route "' . $routeName . '"');
        }
        $route = $app->config['routes'][$routeName];

        // Ensure the route has a URL
        if (!isset($route['url'])) {
            throw new \Exception('Route "' . $routeName . '" must have a "url" property.');
        }
        $url = $route['url'];

        // Get each of the {...} blocks in the URL
        preg_match_all('@{([A-Za-z_]+)}@', $url, $placeholders);

        // If no parameters need substituting...
        if (!isset($placeholders[1])) {
            // Simply return the URL
            return $url;
        }

        // Swap all the parameters
        foreach ($placeholders[1] as $placeholder) {

            if (!array_key_exists($placeholder, $parameters)) {
                throw new \Exception(
                    'Parameter "' . $placeholder . '" must be specified to ' .
                    'generate URL for route "' . $routeName . '"'
                );
            }

            // Replace it with the value from the array
            $url = str_replace('{' . $placeholder . '}', $parameters[$placeholder], $url);
        }

        return $url;
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

            if (($placeholders = $this->match($uri, $route['url'])) !== false) {

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
            throw new \Exception('No route defined for ' . $request->getType() . ' ' . $uri, 404);
        }

        return $request;
    }
}
