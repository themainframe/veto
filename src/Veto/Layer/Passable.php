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

/**
 * Passable
 *
 * @since 0.1
 */
abstract class Passable
{
    /**
     * Indicates that the objects should skip all layers.
     *
     * @var bool
     */
    protected $skipAll;

    /**
     * @param boolean $skipAll
     */
    public function setSkipAll($skipAll = true)
    {
        $this->skipAll = $skipAll;
    }

    /**
     * @return boolean
     */
    public function getSkipAll()
    {
        return $this->skipAll;
    }
}
