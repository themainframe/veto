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

use Veto\HTTP\Request;
use Veto\HTTP\Response;

/**
 * LayerInterface
 *
 * @since 0.1
 */
interface LayerInterface
{
    public function in(Request $request);
    public function out(Response $response);
}
