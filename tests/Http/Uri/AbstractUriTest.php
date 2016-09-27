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

namespace Veto\Tests\Http\Uri;

use Psr\Http\Message\UriInterface;

/**
 * Class implementing shared logic for validation of URI instances against spec.
 */
abstract class AbstractUriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string[] $expected
     * @param UriInterface $uri
     */
    protected function validateInstance(array $expected, UriInterface $uri)
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
