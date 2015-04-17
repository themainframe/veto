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
use Veto\HTTP\Uri;

/**
 * Tests for PSR-7 compliant Uri class.
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromString()
    {
        $hostString = 'http://example.com/foo/bar?baz=bat#qux';

        $uri = Uri::createFromString($hostString);

        $this->validateInstance(
            array(
                'scheme' => 'http',
                'authority' => 'example.com',
                'user_info' => '',
                'host' => 'example.com',
                'port' => null,
                'path' => '/foo/bar',
                'query' => 'baz=bat',
                'fragment' => 'qux'
            ),
            $uri
        );
    }

    public function testCreateFromStringWithNonStandardPort()
    {

    }

    public function testCreateFromStringWithUserInfo()
    {

    }

    // TODO: Tests for path encoding (see docblocks)

    /**
     * @param string[] $expected
     * @param UriInterface $uri
     */
    private function validateInstance(array $expected, UriInterface $uri)
    {
        if (array_key_exists('scheme', $expected)) {
            $this->assertEquals(
                $expected['scheme'],
                $uri->getScheme()
            );
        }

        if (array_key_exists('authority', $expected)) {
            $this->assertEquals(
                $expected['authority'],
                $uri->getAuthority()
            );
        }

        if (array_key_exists('user_info', $expected)) {
            $this->assertEquals(
                $expected['user_info'],
                $uri->getUserInfo()
            );
        }

        if (array_key_exists('host', $expected)) {
            $this->assertEquals(
                $expected['host'],
                $uri->getHost()
            );
        }

        if (array_key_exists('port', $expected)) {
            $this->assertEquals(
                $expected['port'],
                $uri->getPort()
            );
        }

        if (array_key_exists('path', $expected)) {
            $this->assertEquals(
                $expected['path'],
                $uri->getPath()
            );
        }

        if (array_key_exists('query', $expected)) {
            $this->assertEquals(
                $expected['query'],
                $uri->getQuery()
            );
        }

        if (array_key_exists('fragment', $expected)) {
            $this->assertEquals(
                $expected['fragment'],
                $uri->getFragment()
            );
        }
    }
}
