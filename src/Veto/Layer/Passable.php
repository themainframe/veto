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
     * An array of layer names that should always run for this Passable.
     *
     * @var array
     */
    protected $forcedLayers = array();

    /**
     * An array of layer names that should always skip for this Passable.
     *
     * @var array
     */
    protected $skippedLayers = array();

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

    /**
     * Cause this object to skip a named layer.
     *
     * @param string $skippedLayer
     */
    public function skip($skippedLayer)
    {
        $this->skippedLayers[] = $skippedLayer;
    }

    /**
     * Cause this object to always be processed by a named layer.
     *
     * @param string $forcedLayer
     */
    public function force($forcedLayer)
    {
        $this->forcedLayers[] = $forcedLayer;
    }

    /**
     * Check if this object should skip a named layer.
     *
     * @param string $layerName The layer name to check.
     * @return bool
     */
    public function isSkipped($layerName)
    {
        return in_array($layerName, $this->skippedLayers);
    }


    /**
     * Check if this object should always be processed by a named layer.
     *
     * @param string $layerName The layer name to check.
     * @return bool
     */
    public function isForced($layerName)
    {
        return in_array($layerName, $this->forcedLayers);
    }
}
