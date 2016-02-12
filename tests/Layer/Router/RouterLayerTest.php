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

use Veto\Layer\Router\RouterLayer;

/**
 * Tests for the Veto Router Layer
 */
class RouterLayerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create a mock URI instance with the provided path.
     */
    private function buildUriFromPathString($path)
    {
        $stub = $this->getMockBuilder('\Veto\HTTP\Uri')
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getPath')
            ->will($this->returnValue($path));

        return $stub;
    }

    /**
     * Create a mock request that has a given HTTP method and path.
     *
     * @param string $method The HTTP method for the request to have
     * @param string $path The path for the request to have
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function buildRequestForMethodAndPath($method, $path)
    {
        $stub = $this->getMockBuilder('\Veto\HTTP\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getUri')
            ->will($this->returnValue($this->buildUriFromPathString($path)));

        $stub->method('getMethod')
            ->will($this->returnValue($method));

        return $stub;
    }

    /**
     * Check that a simple route matches correctly.
     */
    public function testSimpleRouteMatches()
    {
        $router = new RouterLayer();
        $request = $this->buildRequestForMethodAndPath('GET', '/foo');

        // Register an expectation that the class name and method name will be set as a _controller parameter
        $request->expects($this->once())
            ->method('withParameter')
            ->with(
                $this->equalTo('_controller'),
                $this->equalTo(
                    array('class' => 'class', 'method' => 'method')
                )
            );

        $router->addRoute('foo', '/foo', array('GET'), 'class', 'method');
        $router->in($request);
    }

    /**
     * Check that the router discriminates between HTTP methods correctly.
     */
    public function testMethodsAreConsidered()
    {
        $router = new RouterLayer();
        $postRequest = $this->buildRequestForMethodAndPath('POST', '/foo');

        // Register an expectation that the class name and method name will be set as a _controller parameter
        $postRequest->expects($this->once())
            ->method('withParameter')
            ->with(
                $this->equalTo('_controller'),
                $this->equalTo(
                    array('class' => 'PostClass', 'method' => 'postMethod')
                )
            );


        $router->addRoute('foo', '/foo', array('GET'), 'GetClass', 'getMethod');
        $router->addRoute('foo', '/foo', array('POST'), 'PostClass', 'postMethod');
        $router->in($postRequest);
    }

    /**
     * Check that a parameterised route matches correctly.
     */
    public function testParameterisedRouteMatches()
    {
        $router = new RouterLayer();
        $request = $this->buildRequestForMethodAndPath('GET', '/foo/zoo', 'class', 'method');

        // Register an expectation that the class name and method name will be set as a _controller parameter
        $request

            // withParameter will be called twice, once with the class and method tags, then with the
            // route parameters associated with the matched route
            ->expects($this->exactly(2))
            ->method('withParameter')
            ->withConsecutive(
                array(
                    $this->equalTo('_controller'),
                    $this->equalTo(
                        array('class' => 'class', 'method' => 'method')
                    )
                ),
                array(
                    $this->equalTo('bar'),
                    $this->equalTo('zoo')
                )
            )

            // Each time withParamter is called, return the same object
            ->will($this->returnSelf());

        $router->addRoute('foo', '/foo/{bar}', array('GET'), 'class', 'method');
        $router->in($request);
    }

    /**
     * Check that an exception is thrown if no route matches.
     *
     * @expectedException \Exception
     * @expectedExceptionMessageRegExp #No route defined for#
     */
    public function testExceptionThrownForNoMatch()
    {
        $router = new RouterLayer();
        $request = $this->buildRequestForMethodAndPath('GET', '/bar');
        $router->addRoute('foo', '/foo', array('GET'), 'class', 'method');
        $router->in($request);
    }

    /**
     * Check that the URL for a simple route generates correctly.
     */
    public function testGenerateUrlForSimpleRoute()
    {
        $router = new RouterLayer();
        $router->addRoute('foo', '/foo', array('GET'), 'class', 'method');
        $this->assertEquals('/foo', $router->generateUrl('foo', array()));
    }

    /**
     * Check that the URL for a parameterised route generates correctly.
     */
    public function testGenerateUrlForParameterisedRoute()
    {
        $router = new RouterLayer();
        $router->addRoute('foo', '/foo/{bar}', array('GET'), 'class', 'method');
        $this->assertEquals('/foo/zoo', $router->generateUrl('foo', array('bar' => 'zoo')));
    }
}
