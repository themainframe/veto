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
 * Tests for the Veto Router Layer parameters functionality
 */
class ParametersTest extends AbstractRouterTest
{
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

            // Each time withParameter is called, return the same object
            ->will($this->returnSelf());

        $router->addRoute('foo', '/foo/{bar}', array(), array('GET'), 'class', 'method');
        $router->in($request);
    }

    /**
     * Check that a parameterised route with a (matching) rule matches correctly.
     */
    public function testParameterisedRouteWithRuleMatches()
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

            // Each time withParameter is called, return the same object
            ->will($this->returnSelf());

        // Add a route for which the 'bar' parameter only matches the string 'zoo'
        $router->addRoute('foo', '/foo/{bar}', array('bar' => 'zoo'), array('GET'), 'class', 'method');
        $router->in($request);
    }

    /**
     * Check that a parameterised route with a (broken) rule does not match.
     *
     * @expectedException \Exception
     * @expectedExceptionMessageRegExp #No route defined for#
     */
    public function testParameterisedRouteWithBrokenRuleDoesNotMatch()
    {
        $router = new RouterLayer();
        $request = $this->buildRequestForMethodAndPath('GET', '/foo/zoo', 'class', 'method');

        // Add a route for which the 'bar' parameter only matches the string 'boo'
        $router->addRoute('foo', '/foo/{bar}', array('bar' => 'boo'), array('GET'), 'class', 'method');
        $router->in($request);
    }
}
