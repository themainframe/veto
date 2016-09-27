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

use Veto\Http\Request;

/**
 * InboundLayerInterface
 */
interface InboundLayerInterface
{
    public function in(Request $request);
}
