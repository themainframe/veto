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
namespace Veto\Twig\Layer;

use Veto\HTTP\Response;
use Veto\Layer\AbstractLayer;
use Veto\Twig\HTTP\TwigResponse;

/**
 * TwigLayer
 * Renders templates using twig.
 *
 * @since 0.1
 */
class TwigLayer extends AbstractLayer
{
    /**
     * Twig.
     *
     * @var \Twig_Environment
     */
    private $twig;

    function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function out(Response $response)
    {
        if ($response instanceof TwigResponse) {
            // Process this response
            $response->setContent(
                $this->twig->render($response->getTemplate(), $response->parameters->all())
            );
        }

        return $response;
    }
}
