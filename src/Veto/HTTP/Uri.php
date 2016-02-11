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
namespace Veto\HTTP;

use Psr\Http\Message\UriInterface;
use Veto\Collection\Bag;

/**
 * HTTP URI Value Type
 *
 * Substantially based on Slim Framework's URI implementation.
 *
 * @link https://github.com/slimphp/Slim/
 */
class Uri implements UriInterface
{
    /**
     * The URI scheme (without "://" suffix)
     *
     * @var string
     */
    protected $scheme = '';

    /**
     * The user info encoded in the url (for URLS like http://user@password:example.com
     *
     * @var string Either empty or encoded in form username[:password]
     */
    protected $userInfo = '';

    /**
     * The URI host
     *
     * @var string
     */
    protected $host = '';

    /**
     * The URI port number
     *
     * @var int
     */
    protected $port;

    /**
     * The URI path
     *
     * @var string
     */
    protected $path = '';

    /**
     * The URI query string (without "?" prefix)
     *
     * @var string
     */
    protected $query = '';

    /**
     * The URI fragment string (without "#" prefix)
     *
     * @var string
     */
    protected $fragment = '';

    /**
     * Create a new URI
     *
     * @param string $scheme URI scheme
     * @param string $host URI host
     * @param int|null $port URI port number
     * @param string $path URI path
     * @param string $query URI query string
     * @param string $fragment
     * @param string $userInfo (optional) username & password encoded in URI
     */
    public function __construct($scheme, $host, $port = null, $path = '/', $query = '', $fragment = '', $userInfo = '')
    {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->path = empty($path) ? '/' : $path;
        $this->query = $query;
        $this->fragment = $fragment;
        $this->userInfo = $userInfo;

        // Default ports SHOULD be null
        $this->port = $port;
        if ($this->isStandardPort($port, $scheme)) {
            $this->port = null;
        }
    }

    /**
     * Create new Uri from string
     *
     * Substantially based on Slim Framework's URI implementation.
     *
     * @param  string $uri Complete Uri string (i.e., https://user:pass@host:443/path?query)
     * @link https://github.com/slimphp/Slim/
     * @throws \InvalidArgumentException
     * @return self
     */
    public static function createFromString($uri)
    {
        // Check for valid argument type
        if (!is_string($uri) && !method_exists($uri, '__toString')) {
            throw new \InvalidArgumentException(
                '\Veto\HTTP\Uri::createFromString() argument must be a string'
            );
        }

        // Normalize URL before validation as filter_var requires this for a valid url
        $mungedUri = $uri;
        if (!array_key_exists('scheme', parse_url($uri))) {
            $mungedUri = 'http://' . $uri;
        }

        $parts = parse_url($mungedUri);

        // Ensure that the URL is valid
        if (!filter_var($mungedUri, FILTER_VALIDATE_URL) || !strlen($parts['host'])) {
            throw new \InvalidArgumentException(
                'Call to \\' . __METHOD__ . '() with invalid URI "' . $uri . '"'
            );
        }

        $scheme = $parts['scheme'];
        $user = isset($parts['user']) ? $parts['user'] : '';
        $pass = isset($parts['pass']) ? $parts['pass'] : '';
        $host = $parts['host'];
        $port = isset($parts['port']) ? $parts['port'] : null;
        $path = isset($parts['path']) ? $parts['path'] : '';
        $query = isset($parts['query']) ? $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? $parts['fragment'] : '';
        $userInfo = static::buildUserInfo($user, $pass);

        return new static($scheme, $host, $port, $path, $query, $fragment, $userInfo);
    }

    /**
     * Create new URI from the provided environment
     *
     * Substantially based on Slim Framework's URI implementation.
     *
     * @param Bag $environment
     * @link https://github.com/slimphp/Slim/
     * @return self
     */
    public static function createFromEnvironment(Bag $environment)
    {
        // Scheme
        if ($environment->has('HTTP_X_FORWARDED_PROTO') === true) {
            $scheme = $environment->get('HTTP_X_FORWARDED_PROTO');
        } else {
            $https = $environment->get('HTTPS', '');
            $scheme = empty($https) || $https === 'off' ? 'http' : 'https';
        }

        // Authority
        $user = $environment->get('PHP_AUTH_USER', '');
        $password = $environment->get('PHP_AUTH_PW', '');
        $host = $environment->get('HTTP_HOST', $environment->get('SERVER_NAME'));
        $port = (int)$environment->get('SERVER_PORT', null);

        // Path
        $requestUri = parse_url($environment->get('REQUEST_URI'), PHP_URL_PATH);
        $path = '/' . ltrim($requestUri, '/');

        // Query string
        $queryString = $environment->get('QUERY_STRING', '');

        // Fragment
        $fragment = '';

        $userInfo = static::buildUserInfo($user, $password);

        // Build Uri
        return new static($scheme, $host, $port, $path, $queryString, $fragment, $userInfo);
    }

    /**
     * Build user info string from username/password components.
     *
     * @param string $username
     * @param string $password
     * @return string
     */
    protected static function buildUserInfo($username, $password)
    {
        $userInfo = '';
        if (strlen($username)) {
            $userInfo = $username;

            if (strlen($password)) {
                $userInfo .= ':' . $password;
            }
        }

        return $userInfo;
    }

    /**
     * Retrieve the URI scheme.
     *
     * Implementations SHOULD restrict values to "http", "https", or an empty
     * string but MAY accommodate other schemes if required.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The string returned MUST omit the trailing "://" delimiter if present.
     *
     * @return string The scheme of the URI.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Retrieve the authority portion of the URI.
     *
     * The authority portion of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * This method MUST return an empty string if no authority information is
     * present.
     *
     * @return string Authority portion of the URI, in "[user-info@]host[:port]"
     *     format.
     */
    public function getAuthority()
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();
        $showPort = !$this->isStandardPort($port, $this->scheme);

        return ($userInfo ? $userInfo . '@' : '') . $host . ($port && $showPort ? ':' . $port : '');
    }

    /**
     * Retrieve the user information portion of the URI, if present.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * Implementations MUST NOT return the "@" suffix when returning this value.
     *
     * @return string User information portion of the URI, if present, in
     *     "username[:password]" format.
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Retrieve the host segment of the URI.
     *
     * This method MUST return a string; if no host segment is present, an
     * empty string MUST be returned.
     *
     * @return string Host segment of the URI.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retrieve the port segment of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The port for the URI.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Retrieve the path segment of the URI.
     *
     * This method MUST return a string; if no path is present it MUST return
     * the string "/".
     *
     * @return string The path segment of the URI.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * This method MUST return a string; if no query string is present, it MUST
     * return an empty string.
     *
     * The string returned MUST omit the leading "?" character.
     *
     * @return string The URI query string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment segment of the URI.
     *
     * This method MUST return a string; if no fragment is present, it MUST
     * return an empty string.
     *
     * The string returned MUST omit the leading "#" character.
     *
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Create a new instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified scheme. If the scheme
     * provided includes the "://" delimiter, it MUST be removed.
     *
     * Implementations SHOULD restrict values to "http", "https", or an empty
     * string but MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return self A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    /**
     * Create a new instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string $user User name to use for authority.
     * @param null|string $password Password associated with $user.
     * @return self A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $clone = clone $this;

        $clone->userInfo = static::buildUserInfo($user, $password);

        return $clone;
    }

    /**
     * Create a new instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host Hostname to use with the new instance.
     * @return self A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    /**
     * Create a new instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port Port to use with the new instance; a null value
     *     removes the port information.
     * @return self A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    /**
     * Create a new instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified path.
     *
     * The path MUST be prefixed with "/"; if not, the implementation MAY
     * provide the prefix itself.
     *
     * The implementation MUST percent-encode reserved characters as
     * specified in RFC 3986, Section 2, but MUST NOT double-encode any
     * characters.
     *
     * An empty path value is equivalent to removing the path.
     *
     * @param string $path The path to use with the new instance.
     * @return self A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    /**
     * Create a new instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified query string.
     *
     * If the query string is prefixed by "?", that character MUST be removed.
     * Additionally, the query string SHOULD be parseable by parse_str() in
     * order to be valid.
     *
     * The implementation MUST percent-encode reserved characters as
     * specified in RFC 3986, Section 2, but MUST NOT double-encode any
     * characters.
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return self A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        $clone = clone $this;
        $clone->query = $query;

        return $clone;
    }

    /**
     * Create a new instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified URI fragment.
     *
     * If the fragment is prefixed by "#", that character MUST be removed.
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The URI fragment to use with the new instance.
     * @return self A new instance with the specified URI fragment.
     */
    public function withFragment($fragment)
    {
        $clone = clone $this;
        $clone->fragment = $fragment;

        return $clone;
    }

    /**
     * Convert this URI to the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        return ($scheme ? $scheme . '://' : '') . $authority . $path . ($query ? '?' . $query : '') . ($fragment ? '#' . $fragment : '');
    }

    /**
     * Does this URI use a standard port?
     *
     * @param int|null $port
     * @param string $scheme
     *
     * @return bool
     */
    protected function isStandardPort($port, $scheme)
    {
        return ($scheme === 'http' && $port === 80) ||
            ($scheme === 'https' && $port === 443);
    }
}
