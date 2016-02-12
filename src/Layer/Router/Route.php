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
use Veto\HTTP\Request;

/**
 * Route
 * Represents a route within the application.
 *
 * @since 0.1
 */
class Route
{
    /**
     * The pattern to match.
     *
     * @var string
     */
    protected $pattern;

    /**
     * The name of the route.
     *
     * @var string
     */
    protected $name;

    /**
     * A list of supported methods.
     *
     * @var string[]
     */
    protected $methods;

    /**
     * The controller that handles the route.
     *
     * @var string
     */
    protected $controller;

    /**
     * The action that handles the route.
     *
     * @var string
     */
    protected $action;

    /**
     * Create a new route.
     *
     * @param string $name The name of the route.
     * @param string $pattern The route pattern string.
     * @param array $methods The methods that can be used for this route.
     * @param string $controller The controller that handles the route.
     * @param string $action The action that handles the route.
     */
    public function __construct($name, $pattern, array $methods, $controller, $action)
    {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->methods = $methods;
        $this->controller = $controller;
        $this->action = $action;
    }

    /**
     * Check if the request matches a defined route pattern.
     *
     * Returns an array containing any route parameters on a successful match.
     * Returns false for failed matches.
     *
     * @param Request $request The request to check.
     * @return array|bool
     */
    public function matches(Request $request)
    {
        // Verify that the method is appropriate
        if (!in_array($request->getMethod(), $this->methods)) {
            return false;
        }

        // Check if the pattern contains any {...} blocks
        preg_match_all('@{([A-Za-z_]+)}@', $this->pattern, $placeholders);

        if ($placeholders && isset($placeholders[1])) {

            // Convert all {...} blocks into regex groups
            $pattern = preg_replace('@{[A-Za-z_]+}@', '([^/]+)', $this->pattern);

            // Get the placeholder names
            $placeholders = $placeholders[1];

            // See if the route matches
            if (preg_match('@^' . $pattern . '$@', $request->getUri()->getPath(), $matches)) {
                // Merge the placeholder names with their URI values
                array_shift($matches);
                return array_combine($placeholders, $matches);
            }
        }

        // Simple matching - check if the URI matches the pattern
        return $request->getUri() == $this->pattern ? array() : false;
    }


    /**
     * Generate a URL from a route name and parameter set.
     *
     * @param array $parameters Optionally the parameters to use.
     * @throws \Exception
     * @return string|boolean The URL on success, false on failure.
     */
    public function generateUrl(array $parameters = array())
    {
        $url = $this->pattern;

        // Get each of the {...} blocks in the URL
        preg_match_all('@{([A-Za-z_]+)}@', $url, $placeholders);

        // Swap all the parameters
        foreach ($placeholders[1] as $placeholder) {

            if (!array_key_exists($placeholder, $parameters)) {
                throw new \Exception(
                    'Parameter "' . $placeholder . '" must be specified to ' .
                    'generate URL for route "' . $this->name . '"'
                );
            }

            // Replace it with the value from the array
            $url = str_replace('{' . $placeholder . '}', $parameters[$placeholder], $url);
        }

        return $url;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string[] $methods
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;
    }

    /**
     * @return string[]
     */
    public function getMethods()
    {
        return $this->methods;
    }
}
