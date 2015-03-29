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
 * The Veto Event Dispatcher.
 *
 * Implements an event dispatcher with deferred priority sorting.
 */
class Dispatcher
{
    /**
     * The registered listeners for this Event Dispatcher
     *
     * @var array
     */
    private $listeners = array();

    /**
     * An associative array mapping each event name to the flag indicating if sorting is required
     *
     * @var array
     */
    private $sortingRequiredFlags = array();

    /**
     * Register a listener with this event dispatcher.
     *
     * @param string $eventName The name of the event to listen for
     * @param callable $callable The callable to dispatch events to
     * @throws \InvalidArgumentException
     */
    public function listen($eventName, $callable, $priority)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(
                'Veto\Event\Dispatcher::listen() - $callable must be a callable value'
            );
        }

        $this->listeners[$eventName][$priority][] = $callable;
        $this->sortingRequiredFlags[$eventName] = true;
    }

    /**
     * Dispatches an event to any associated listeners.
     *
     * @param Event $event
     * @return int The number of listeners fired
     */
    public function dispatch($eventName, Event $event)
    {
        if (isset($this->listeners[$eventName])) {

            // Does the list need to be priority-sorted?
            if ($this->sortingRequiredFlags[$eventName]) {
                $this->sortListeners($eventName);
            }

            foreach ($this->listeners[$eventName] as $listener) {

                // Pass the event, the name and this dispatcher instance
                call_user_func($listener, $event, $eventName, $this);

                // If the propagation was stopped, do not dispatch to any more listeners
                if ($event->isPropagationStopped()) {
                    break;
                }
            }
        }
    }

    /**
     * Sort the internal representation of the listener list for an event name.
     *
     * @param string $eventName
     */
    private function sortListeners($eventName)
    {
        ksort($this->listeners[$eventName]);
        $this->sortingRequiredFlags[$eventName] = false;
    }
}
