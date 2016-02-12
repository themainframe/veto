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
 * PhpEngine
 * A basic PHP-based template engine for rendering old-style PHP viewscripts. Used internally by Veto for rendering
 * exception and debug pages.
 *
 * @since 0.1
 */
class PhpEngine implements EngineInterface
{
    /**
     * @var string[]
     */
    private $paths;

    /**
     * PhpEngine constructor.
     */
    public function __construct()
    {
        $this->paths = array();
    }

    /**
     * Add a template directory to look for template files inside.
     *
     * @param string $path The path to a template directory to load templates from.
     * @return boolean
     */
    public function addPath($path)
    {
        $this->paths[] = $path;

        return true;
    }

    /**
     * Render the given template name and return the result as a string.
     *
     * @param string $templateName The name of the template to render
     * @param array $parameters Any parameters to render the template with
     * @throws \Exception
     * @return string
     */
    public function render($templateName, array $parameters = array())
    {
        foreach ($this->paths as $path) {
            $fullPath = $path . DIRECTORY_SEPARATOR . $templateName;
            if (file_exists($fullPath)) {
                extract($parameters, EXTR_SKIP);
                ob_start();
                require $fullPath;
                return ob_get_clean();
            }
        }

        throw new \RuntimeException(sprintf('The template %s cannot be found.', $templateName));
    }
}
