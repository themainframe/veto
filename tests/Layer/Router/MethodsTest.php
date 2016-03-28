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
 * Tests for the Veto Router Layer methods functionality
 */
class MethodsTest extends AbstractRouterTest
{
    /**
     * Check that a simple route without a method specification matches correctly.
     * Routes without a method specified should match for all methods.
     */
    public function testMethodlessRouteMatches()
    {
        $router = new RouterLayer();

        // Register an expectation that the class name and method name will be set as a _controller parameter
        $getRequest = $this->buildRequestForMethodAndPath('GET', '/foo');
        $getRequest->expects($this->once())
            ->method('withParameter')
            ->with(
                $this->equalTo('_controller'),
                $this->equalTo(
                    array('class' => 'class', 'method' => 'method')
                )
            );

        $router->addRoute('foo', '/foo', array(), array(), 'class', 'method');
        $router->in($getRequest);

        // Now try for a POST request
        $postRequest = $this->buildRequestForMethodAndPath('POST', '/foo');
        $postRequest->expects($this->once())
            ->method('withParameter')
            ->with(
                $this->equalTo('_controller'),
                $this->equalTo(
                    array('class' => 'class', 'method' => 'method')
                )
            );
        $router->in($postRequest);
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


        $router->addRoute('foo', '/foo', array(), array('GET'), 'GetClass', 'getMethod');
        $router->addRoute('foo', '/foo', array(), array('POST'), 'PostClass', 'postMethod');
        $router->in($postRequest);
    }
}
