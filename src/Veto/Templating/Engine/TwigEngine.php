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
namespace Veto\Templating\Engine;

use Veto\Templating\EngineInterface;

/**
 * TwigEngine
 * A bridge between Veto and the Twig templating engine. Renders Twig templates.
 *
 * @since 0.1
 */
class TwigEngine implements EngineInterface
{
    protected $environment;

    public function __construct(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Add a template directory to look for template files inside.
     *
     * @param string $path The path to a template directory to load templates from.
     * @return boolean
     */
    public function addPath($path)
    {
        // Also register the location of the exception views
        $loader = $this->environment->getLoader();

        // Determine the type of loader that is used by this twig
        if ($loader instanceof \Twig_Loader_Filesystem) {
            $loader->addPath($path);
        } else if($loader instanceof \Twig_Loader_Chain) {
            $loader->addLoader(
                new \Twig_Loader_Filesystem($path)
            );
        } else {
            return false;
        }

        return true;
    }

    /**
     * Render the given template name and return the result as a string.
     *
     * @param string $templateName The name of the template to render
     * @param array $parameters Any parameters to render the template with
     * @return string
     */
    public function render($templateName, array $parameters = array())
    {
        return $this->environment->render($templateName, $parameters);
    }
}
