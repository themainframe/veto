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

/**
 * Tests for the Veto Router Layer
 */
abstract class AbstractRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create a mock URI instance with the provided path.
     *
     * @param string $path
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildUriFromPathString($path)
    {
        $stub = $this->getMockBuilder('\Veto\Http\Uri')
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
    protected function buildRequestForMethodAndPath($method, $path)
    {
        $stub = $this->getMockBuilder('\Veto\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('getUri')
            ->will($this->returnValue($this->buildUriFromPathString($path)));

        $stub->method('getMethod')
            ->will($this->returnValue($method));

        return $stub;
    }
}
