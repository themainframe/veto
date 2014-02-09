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

use Veto\App;

/**
 * LayerInterface
 *
 * @since 0.1
 */
interface LayerInterface
{
    public function __construct(App $app);
}
