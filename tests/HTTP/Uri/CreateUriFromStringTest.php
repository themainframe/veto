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

namespace Veto\Tests\HTTP\Uri;

use Psr\Http\Message\UriInterface;
use Veto\HTTP\Uri;

/**
 * Tests for creating PSR-7 compliant URI from URI string
 */
class CreateUriFromStringTest extends AbstractUriTest
{
    /**
     * @dataProvider uriProvider
     *
     * @param string $uri
     * @param string[] $validationData
     */
    public function testCreateFromString($uri, array $validationData)
    {
        $uri = Uri::createFromString($uri);

        $this->validateInstance(
            $validationData,
            $uri
        );
    }

    public function testCreateFromStringWithUserInfo()
    {
        $hostString = 'http://bob:password@example.com/foo/bar?baz=bat#qux';

        $uri = Uri::createFromString($hostString);

        $this->validateInstance(
            array(
                'scheme' => 'http',
                'authority' => 'bob:password@example.com',
                'user_info' => 'bob:password',
                'host' => 'example.com',
                'port' => null,
                'path' => '/foo/bar',
                'query' => 'baz=bat',
                'fragment' => 'qux'
            ),
            $uri
        );
    }

    // TODO: Tests for handling of path encoding (see UriInterface::getPath docblocks)

    public function testErrorCreateFromStringNotString()
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            '\Veto\HTTP\Uri::createFromString() argument must be a string'
        );

        Uri::createFromString(new \StdClass);
    }

    public function testErrorCreateFromStringInvalidUri()
    {
        $uri = '';

        $this->setExpectedException(
            '\InvalidArgumentException',
            'Call to \Veto\HTTP\Uri::createFromString() with invalid URI "' . $uri . '"'
        );

        Uri::createFromString($uri);
    }

    public function uriProvider()
    {
        return array(
            'simple' => array(
                'uri' => 'http://example.com/foo/bar?baz=bat#qux',
                'validation_data' => array(
                    'scheme' => 'http',
                    'authority' => 'example.com',
                    'user_info' => '',
                    'host' => 'example.com',
                    'port' => null,
                    'path' => '/foo/bar',
                    'query' => 'baz=bat',
                    'fragment' => 'qux'
                )
            ),
            'object-to-string' => array(
                'uri' => new Uri(
                    'http',
                    'example.com',
                    null,
                    '/foo/bar'
                ),
                'validation_data' => array(
                    'scheme' => 'http',
                    'authority' => 'example.com',
                    'user_info' => '',
                    'host' => 'example.com',
                    'port' => null,
                    'path' => '/foo/bar',
                    'query' => '',
                    'fragment' => ''
                )
            ),
            'non-standard-port' => array(
                'uri' => 'http://example.com:8080/foo/bar?baz=bat#qux',
                'validation_data' => array(
                    'scheme' => 'http',
                    'authority' => 'example.com:8080',
                    'user_info' => '',
                    'host' => 'example.com',
                    'port' => 8080,
                    'path' => '/foo/bar',
                    'query' => 'baz=bat',
                    'fragment' => 'qux'
                )
            ),
            'user-info' => array(
                'uri' => 'http://bob:password@example.com/foo/bar?baz=bat#qux',
                'validation_data' => array(
                    'scheme' => 'http',
                    'authority' => 'bob:password@example.com',
                    'user_info' => 'bob:password',
                    'host' => 'example.com',
                    'port' => null,
                    'path' => '/foo/bar',
                    'query' => 'baz=bat',
                    'fragment' => 'qux'
                )
            )
        );
    }
}
