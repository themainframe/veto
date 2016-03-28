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
 * Tests for the Veto Router URL generation functionality.
 */
class UrlGenerationTest extends AbstractRouterTest
{
    /**
     * Check that the URL for a simple route generates correctly.
     */
    public function testGenerateUrlForSimpleRoute()
    {
        $router = new RouterLayer();
        $router->addRoute('foo', '/foo', array(), array(), 'class', 'method');
        $this->assertEquals('/foo', $router->generateUrl('foo', array()));
    }

    /**
     * Check that the URL for a parameterised route generates correctly.
     */
    public function testGenerateUrlForParameterisedRoute()
    {
        $router = new RouterLayer();
        $router->addRoute('foo', '/foo/{bar}', array(), array('GET'), 'class', 'method');
        $this->assertEquals('/foo/zoo', $router->generateUrl('foo', array('bar' => 'zoo')));
    }
}
