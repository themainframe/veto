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
    /**
     * @dataProvider environmentProvider
     *
     * @param Bag $environment
     * @param string[] $validationData
     */
    public function testCreateFromEnvironment(Bag $environment, array $validationData)
    {
        $uri = Uri::createFromEnvironment($environment);

        $this->validateInstance(
            $validationData,
            $uri
        );
    }

    public function environmentProvider()
    {
        return array(
            array(
                'environment' => new Bag(array(
                    'HTTP_HOST' => 'example.com',
                    'SERVER_PORT' => '80',
                    'SCRIPT_NAME' => '/foo/bar',
                    'REQUEST_URI' => '/foo/bar',
                    'QUERY_STRING' => 'baz=bat'
                )),
                'validation_data' => array(
                    'scheme' => 'http',
                    'authority' => 'example.com',
                    'user_info' => '',
                    'host' => 'example.com',
                    'port' => null,
                    'path' => '/foo/bar',
                    'query' => 'baz=bat'
                )
            ),
            'non-standard-port' => array(
                'environment' => new Bag(array(
                    'HTTP_HOST' => 'example.com',
                    'SERVER_PORT' => '8080',
                    'SCRIPT_NAME' => '/foo/bar',
                    'REQUEST_URI' => '/foo/bar',
                    'QUERY_STRING' => 'baz=bat'
                )),
                'validation_data' => array(
                    'scheme' => 'http',
                    'authority' => 'example.com:8080',
                    'user_info' => '',
                    'host' => 'example.com',
                    'port' => 8080,
                    'path' => '/foo/bar',
                    'query' => 'baz=bat'
                )
            ),
            'user-info' => array(
                'environment' => new Bag(array(
                    'PHP_AUTH_USER' => 'bob',
                    'PHP_AUTH_PW' => 'password',
                    'HTTP_HOST' => 'example.com',
                    'SERVER_PORT' => '80',
                    'SCRIPT_NAME' => '/foo/bar',
                    'REQUEST_URI' => '/foo/bar',
                    'QUERY_STRING' => 'baz=bat'
                )),
                'validation_data' => array(
                    'scheme' => 'http',
                    'authority' => 'bob:password@example.com',
                    'user_info' => 'bob:password',
                    'host' => 'example.com',
                    'port' => null,
                    'path' => '/foo/bar',
                    'query' => 'baz=bat'
                )
            ),
            'x-forwarded-for' => array(
                'environment' => new Bag(array(
                    'HTTP_X_FORWARDED_PROTO' => 'https',
                    'HTTP_HOST' => 'example.com',
                    'SERVER_PORT' => '443',
                    'SCRIPT_NAME' => '/foo/bar',
                    'REQUEST_URI' => '/foo/bar',
                    'QUERY_STRING' => 'baz=bat'
                )),
                'validation_data' => array(
                    'scheme' => 'https',
                    'authority' => 'example.com',
                    'user_info' => '',
                    'host' => 'example.com',
                    'port' => null,
                    'path' => '/foo/bar',
                    'query' => 'baz=bat'
                )
            ),
            'https' => array(
                'environment' => new Bag(array(
                    'HTTPS' => 'on',
                    'HTTP_HOST' => 'example.com',
                    'SERVER_PORT' => '443',
                    'SCRIPT_NAME' => '/foo/bar',
                    'REQUEST_URI' => '/foo/bar',
                    'QUERY_STRING' => 'baz=bat'
                )),
                'validation_data' => array(
                    'scheme' => 'https',
                    'authority' => 'example.com',
                    'user_info' => '',
                    'host' => 'example.com',
                    'port' => null,
                    'path' => '/foo/bar',
                    'query' => 'baz=bat'
                )
            )
        );
    }
}
