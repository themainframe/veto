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
 * NullEngine
 * A templating engine that does nothing.
 *
 * @since 0.1
 */
class NullEngine implements EngineInterface
{
    /**
     * Add a template directory to look for template files inside.
     *
     * @param string $path The path to a template directory to load templates from.
     * @return boolean
     */
    public function addPath($path)
    {
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
        throw new \Exception('NullEngine cannot render templates.');
    }
}
