<?php
/**
 * Veto.
 * PHP Microframework.
 *
 * @author brian ridley <ptlis@ptlis.net>
 * @copyright Damien Walsh 2013-2015
 * @version 0.1
 * @package veto
 */

namespace Veto\Tests\HTTP;

use Psr\Http\Message\UriInterface;
use Veto\Collection\Bag;
use Veto\HTTP\Uri;

/**
 * Tests for creating PSR-7 compliant URI from application environment.
 */
class CreateUriFromEnvironmentTest extends AbstractUriTest
{
    public function testCreateFromEnvironment()
    {
        $bag = new Bag(array(
            'HTTP_HOST' => 'example.com',
            'SERVER_PORT' => '80',
            'SCRIPT_NAME' => '/foo/bar',
            'REQUEST_URI' => '/foo/bar',
            'QUERY_STRING' => 'baz=bat'
        ));

        $uri = Uri::createFromEnvironment($bag);

        $this->validateInstance(
            array(
                'scheme' => 'http',
                'authority' => 'example.com',
                'user_info' => '',
                'host' => 'example.com',
                'port' => null,
                'path' => '/foo/bar',
                'query' => 'baz=bat'
            ),
            $uri
        );
    }

    public function testCreateFromEnvironmentWithNonStandardPort()
    {
        $bag = new Bag(array(
            'HTTP_HOST' => 'example.com',
            'SERVER_PORT' => '8080',
            'SCRIPT_NAME' => '/foo/bar',
            'REQUEST_URI' => '/foo/bar',
            'QUERY_STRING' => 'baz=bat'
        ));

        $uri = Uri::createFromEnvironment($bag);

        $this->validateInstance(
            array(
                'scheme' => 'http',
                'authority' => 'example.com:8080',
                'user_info' => '',
                'host' => 'example.com',
                'port' => 8080,
                'path' => '/foo/bar',
                'query' => 'baz=bat'
            ),
            $uri
        );
    }

    public function testCreateFromEnvironmentWithUserInfo()
    {
        $bag = new Bag(array(
            'PHP_AUTH_USER' => 'bob',
            'PHP_AUTH_PW' => 'password',
            'HTTP_HOST' => 'example.com',
            'SERVER_PORT' => '80',
            'SCRIPT_NAME' => '/foo/bar',
            'REQUEST_URI' => '/foo/bar',
            'QUERY_STRING' => 'baz=bat'
        ));

        $uri = Uri::createFromEnvironment($bag);

        $this->validateInstance(
            array(
                'scheme' => 'http',
                'authority' => 'bob:password@example.com',
                'user_info' => 'bob:password',
                'host' => 'example.com',
                'port' => null,
                'path' => '/foo/bar',
                'query' => 'baz=bat'
            ),
            $uri
        );
    }

    public function testCreateFromEnvironmentWithXForwardedFor()
    {
        $bag = new Bag(array(
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_HOST' => 'example.com',
            'SERVER_PORT' => '80',
            'SCRIPT_NAME' => '/foo/bar',
            'REQUEST_URI' => '/foo/bar',
            'QUERY_STRING' => 'baz=bat'
        ));

        $uri = Uri::createFromEnvironment($bag);

        $this->validateInstance(
            array(
                'scheme' => 'https',
                'authority' => 'example.com',
                'user_info' => '',
                'host' => 'example.com',
                'port' => null,
                'path' => '/foo/bar',
                'query' => 'baz=bat'
            ),
            $uri
        );
    }
}
