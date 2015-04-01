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
namespace Veto\Tests\Event;
use Veto\Event\Dispatcher;
use Veto\Event\Event;

/**
 * Tests for the Veto Event Dispatcher.
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /* Event names */
    const EVENT_FOO = 'event.foo';
    const EVENT_BAR = 'event.bar';

    /* Methods to call */
    const LISTENER_METHOD = 'foo';

    private function createDispatcherInstance()
    {
        return new Dispatcher();
    }

    private function createListenerInstance()
    {
        $mock = $this->getMockBuilder('stdClass')
            ->setMethods(array(self::LISTENER_METHOD))
            ->getMock();

        return $mock;
    }

    public function testAddListener()
    {
        $dispatcher = $this->createDispatcherInstance();
        $dispatcher->listen(self::EVENT_FOO, function() {});
        $this->assertCount(1, $dispatcher->getListeners());
    }

    public function testListenerDidReceiveEvent()
    {
        $dispatcher = $this->createDispatcherInstance();

        // Set up the listener
        $listener = $this->createListenerInstance();
        $listener
            ->expects($this->exactly(1))
            ->method(self::LISTENER_METHOD);

        $dispatcher->listen(self::EVENT_FOO, array($listener, self::LISTENER_METHOD));
        $dispatcher->dispatch(self::EVENT_FOO, new Event());
    }

    public function testListenerWasPassedEventInstance()
    {
        $dispatcher = $this->createDispatcherInstance();

        // Set up the event object
        $event = new Event();

        // Set up the listener
        $listener = $this->createListenerInstance();
        $listener
            ->expects($this->exactly(1))
            ->method(self::LISTENER_METHOD)
            ->with($this->identicalTo($event));

        $dispatcher->listen(self::EVENT_FOO, array($listener, self::LISTENER_METHOD));
        $dispatcher->dispatch(self::EVENT_FOO, $event);
    }

    /**
     * @depends testListenerWasPassedEventInstance
     */
    public function testListenerWasPassedEventName()
    {
        $dispatcher = $this->createDispatcherInstance();

        // Set up the event object
        $event = new Event();

        // Set up the listener
        $listener = $this->createListenerInstance();
        $listener
            ->expects($this->exactly(1))
            ->method(self::LISTENER_METHOD)

            // This time also check that the event name was passed too
            ->with($this->identicalTo($event), $this->equalTo(self::EVENT_FOO));

        $dispatcher->listen(self::EVENT_FOO, array($listener, self::LISTENER_METHOD));
        $dispatcher->dispatch(self::EVENT_FOO, $event);
    }

    /**
     * @depends testListenerWasPassedEventName
     */
    public function testListenerWasPassedDispatcherInstance()
    {
        $dispatcher = $this->createDispatcherInstance();

        // Set up the event object
        $event = new Event();

        // Set up the listener
        $listener = $this->createListenerInstance();
        $listener
            ->expects($this->exactly(1))
            ->method(self::LISTENER_METHOD)

            // This time also check that the dispatcher instance was passed too
            ->with($this->identicalTo($event), $this->equalTo(self::EVENT_FOO), $this->identicalTo($dispatcher));

        $dispatcher->listen(self::EVENT_FOO, array($listener, self::LISTENER_METHOD));
        $dispatcher->dispatch(self::EVENT_FOO, $event);
    }
}
