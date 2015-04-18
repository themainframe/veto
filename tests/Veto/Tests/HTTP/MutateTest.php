<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Veto\Tests\HTTP;


use Veto\HTTP\Uri;

class MutateTest extends \PHPUnit_Framework_TestCase
{
    public function testWithScheme()
    {
        $originalUri = new Uri(
            'http',
            'example.com',
            null,
            '/'
        );

        $newUri = $originalUri->withScheme('https');

        $this->assertNotSame(
            $originalUri,
            $newUri
        );

        $this->assertEquals(
            'http',
            $originalUri->getScheme()
        );

        $this->assertEquals(
            'https',
            $newUri->getScheme()
        );
    }

    public function testWithUserInfo()
    {
        $originalUri = new Uri(
            'http',
            'example.com',
            null,
            '/'
        );

        $newUri = $originalUri->withUserInfo('bob', 'password');

        $this->assertNotSame(
            $originalUri,
            $newUri
        );

        $this->assertEquals(
            '',
            $originalUri->getUserInfo()
        );

        $this->assertEquals(
            'bob:password',
            $newUri->getUserInfo()
        );
    }

    public function testWithHost()
    {
        $originalUri = new Uri(
            'http',
            'example.com',
            null,
            '/'
        );

        $newUri = $originalUri->withHost('subdomain.example.com');

        $this->assertNotSame(
            $originalUri,
            $newUri
        );

        $this->assertEquals(
            'example.com',
            $originalUri->getHost()
        );

        $this->assertEquals(
            'subdomain.example.com',
            $newUri->getHost()
        );
    }

    public function testWithPort()
    {
        $originalUri = new Uri(
            'http',
            'example.com',
            null,
            '/'
        );

        $newUri = $originalUri->withPort(8080);

        $this->assertNotSame(
            $originalUri,
            $newUri
        );

        $this->assertEquals(
            null,
            $originalUri->getPort()
        );

        $this->assertEquals(
            8080,
            $newUri->getPort()
        );
    }

    public function testWithPath()
    {
        $originalUri = new Uri(
            'http',
            'example.com',
            null,
            '/'
        );

        $newUri = $originalUri->withPath('/bar/baz');

        $this->assertNotSame(
            $originalUri,
            $newUri
        );

        $this->assertEquals(
            '/',
            $originalUri->getPath()
        );

        $this->assertEquals(
            '/bar/baz',
            $newUri->getPath()
        );
    }

    public function testWithQuery()
    {
        $originalUri = new Uri(
            'http',
            'example.com',
            null,
            '/'
        );

        $newUri = $originalUri->withQuery('foo=bar');

        $this->assertNotSame(
            $originalUri,
            $newUri
        );

        $this->assertEquals(
            '',
            $originalUri->getQuery()
        );

        $this->assertEquals(
            'foo=bar',
            $newUri->getQuery()
        );
    }

    public function testWithFragment()
    {
        $originalUri = new Uri(
            'http',
            'example.com',
            null,
            '/'
        );

        $newUri = $originalUri->withFragment('qux');

        $this->assertNotSame(
            $originalUri,
            $newUri
        );

        $this->assertEquals(
            '',
            $originalUri->getFragment()
        );

        $this->assertEquals(
            'qux',
            $newUri->getFragment()
        );
    }
}
