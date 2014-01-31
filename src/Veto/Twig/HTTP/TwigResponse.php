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
namespace Veto\Twig\HTTP;

use Veto\Collection\Bag;
use Veto\HTTP\Response;

/**
 * TwigLayer
 * Renders templates using twig.
 *
 * @since 0.1
 */
class TwigResponse extends Response
{
    protected $template;

    public function __construct($template, $parameters = array())
    {
        parent::__construct();

        $this->template = $template;
        $this->parameters = new Bag($parameters);
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
