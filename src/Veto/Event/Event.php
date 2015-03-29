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
namespace Veto\Event;

/**
 * The base class for Events.
 */
class Event
{
    /**
     * When true, the event will not trigger any more handlers.
     *
     * @var boolean
     */
    private $isPropagationStopped;

    /**
     * @return boolean
     */
    public function isPropagationStopped()
    {
        return $this->isPropagationStopped;
    }

    /**
     * @param boolean $isPropagationStopped
     * @return $this
     */
    public function setPropagationStopped($isPropagationStopped)
    {
        $this->isPropagationStopped = $isPropagationStopped;
        return $this;
    }
}
