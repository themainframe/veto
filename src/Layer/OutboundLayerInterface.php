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

use Veto\Http\Response;

/**
 * OutboundLayerInterface
 */
interface OutboundLayerInterface
{
    public function out(Response $response);
}
