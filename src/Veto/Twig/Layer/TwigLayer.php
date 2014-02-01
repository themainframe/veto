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

            // Check if the response specifies a template path
            if ($path = $response->getTemplatePath()) {

                // Get the loader used by twig
                $loader = $this->twig->getLoader();

                // Determine the type of loader that is used by this twig
                if ($loader instanceof \Twig_Loader_Filesystem) {
                    $loader->addPath($path);
                } else if($loader instanceof \Twig_Loader_Chain) {
                    $loader->addLoader(
                        new \Twig_Loader_Filesystem($path)
                    );
                }

                // Can't add a path - don't know how
            }

            // Process this response
            $response->setContent(
                $this->twig->render($response->getTemplate(), $response->parameters->all())
            );
        }

        return $response;
    }
}
