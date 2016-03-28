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
 * Tests for the Veto Router Exceptions.
 */
class ExceptionsTest extends AbstractRouterTest
{
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
        $router->addRoute('foo', '/foo', array(), array('GET'), 'class', 'method');
        $router->in($request);
    }
}
