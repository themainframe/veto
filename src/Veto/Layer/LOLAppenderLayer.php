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

use Veto\HTTP\Response;

/**
 * LOLAppenderLayer
 *
 * @since 0.1
 */
class LOLAppenderLayer extends AbstractLayer
{
    public function out(Response $response)
    {
        return $response->setContent(
            $response->getContent() . ' LOL!'
        );
    }
}
