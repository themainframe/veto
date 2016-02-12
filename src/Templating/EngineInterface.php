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
namespace Veto\Templating;

/**
 * EngineInterface
 * Templating Engine Interface
 *
 * @since 0.1
 */
interface EngineInterface
{
    /**
     * Add a template directory to look for template files inside.
     *
     * @param string $path The path to a template directory to load templates from.
     * @return boolean
     */
    public function addPath($path);

    /**
     * Render the given template name and return the result as a string.
     *
     * @param string $templateName The name of the template to render
     * @param array $parameters Any parameters to render the template with
     * @return string
     */
    public function render($templateName, array $parameters = array());
}
