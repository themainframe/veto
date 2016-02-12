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

namespace Veto\Tests\Layer\Router;

use Veto\Layer\Dispatcher;

/**
 * Tests for the Veto Dispatcher Layer
 */
class DispatcherLayerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create a mock request, optionally tagged with a controller spec.
     *
     * @param bool|array $controllerSpec
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function buildRequestWithControllerSpec($controllerSpec = false)
    {
        $stub = $this->getMockBuilder('\Veto\HTTP\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getParameter')
            ->with($this->equalTo('_controller'))

            // replicate the logic of Request::getParameter
            ->will($this->returnValue($controllerSpec === false ? null : $controllerSpec));

        return $stub;
    }

    /**
     * Create a mock response.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function buildResponse()
    {
        $stub = $this->getMockBuilder('\Veto\HTTP\Response')
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }

    /**
     * Create a mock container that, when a specified service locator is requested, will return a specified service
     * instance.
     *
     * @param string $serviceLocator
     * @param mixed $serviceInstance
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function buildContainerThatExpectsGet($serviceLocator, $serviceInstance)
    {
        $stub = $this->getMockBuilder('\Veto\DI\Container')
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('get')
            ->with($this->equalTo($serviceLocator))
            ->will($this->returnValue($serviceInstance));

        return $stub;
    }

    /**
     * Create a mock controller with a single action method with a specified name. Optionally expect that the action
     * method will be called.
     *
     * @param string $actionMethodName
     * @param bool $expectCall
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function buildControllerWithActionMethod($actionMethodName, $expectCall = false)
    {
        $stub = $this->getMockBuilder('\\\\stdClass')
            ->setMethods(array($actionMethodName))
            ->getMock();

        $stub->expects($expectCall ? $this->once() : $this->never())
            ->method($actionMethodName)
            ->will($this->returnValue($this->buildResponse()));

        return $stub;
    }

    /**
     * Test that untagged requests throw an exception when they are dispatched.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp #The request was not tagged by a router.#
     */
    public function testUntaggedRequestThrowsException()
    {
        // Create a "controller" with a barAction action method and rig up a mock container to provide it
        $controller = $this->buildControllerWithActionMethod('barAction', false);
        $container = $this->buildContainerThatExpectsGet('foo', $controller);

        $layer = new Dispatcher\DispatcherLayer($container);

        // Don't tag the request with a controller spec
        $request = $this->buildRequestWithControllerSpec(false);
        $layer->in($request);
    }

    /**
     * Test that tagged requests are dispatched correctly.
     */
    public function testTaggedRequestIsDispatched()
    {
        // Create a "controller" with a barAction action method and rig up a mock container to provide it
        $controller = $this->buildControllerWithActionMethod('barAction', true);
        $container = $this->buildContainerThatExpectsGet('foo', $controller);

        $layer = new Dispatcher\DispatcherLayer($container);
        $request = $this->buildRequestWithControllerSpec(array(
            'class' => 'foo',
            'method' => 'barAction'
        ));
        $layer->in($request);
    }
}
