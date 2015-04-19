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

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Veto\Collection\Bag;

/**
 * Request
 * @since 0.1
 */
class Request implements RequestInterface
{
    /**
     * The HTTP protocol version
     *
     * @var string
     */
    protected $protocolVersion;

    /**
     * The HTTP method
     *
     * @var string
     */
    protected $method;

    /**
     * The URI
     *
     * @var UriInterface
     */
    protected $uri;

    /**
     * The target of the request
     *
     * @var string
     */
    protected $requestTarget;

    /**
     * The query string parameters
     *
     * @var Bag
     */
    protected $queryParams;

    /**
     * The cookies
     *
     * @var Bag
     */
    protected $cookies;

    /**
     * The headers
     *
     * @var HeaderBag
     */
    protected $headers;

    /**
     * The request parameters
     *
     * @var Bag
     */
    protected $parameters;

    /**
     * The request body
     *
     * @var string
     */
    protected $body;

    /**
     * Create new HTTP request
     *
     * @param string $method The request method
     * @param UriInterface $uri The request URI object
     * @param HeaderBag $headers The request headers collection
     * @param Bag $cookies The request cookies collection
     * @param Bag $serverParams The server environment variables
     * @param StreamInterface $body The request body object
     */
    public function __construct(
        $method,
        UriInterface $uri,
        HeaderBag $headers,
        Bag $cookies,
        Bag $serverParams,
        StreamInterface $body
    ) {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->serverParams = $serverParams;
        $this->parameters = new Bag();
        $this->body = $body;
    }

    /**
     * Helper method to create a new request based on the provided environment Bag.
     *
     * @param Bag $environment
     * @return Request
     */
    public static function createFromEnvironment(Bag $environment)
    {
        $method = $environment->get('REQUEST_METHOD');
        $uri = Uri::createFromEnvironment($environment);
        $headers = HeaderBag::createFromEnvironment($environment);
        $body = new MessageBody(fopen('php://input', 'r'));

        return new self($method, $uri, $headers, new Bag(), $environment, $body);
    }

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Create a new instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     * @return self
     */
    public function withProtocolVersion($version)
    {
        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        return $this->headers->has($name);
    }

    /**
     * Create a new instance with the provided header, replacing any existing
     * values of any headers with the same case-insensitive name.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new and/or updated header and value.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers = new HeaderBag();
        $clone->headers->add($name, $value);

        return $clone;
    }

    /**
     * Creates a new instance, with the specified header appended with the
     * given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new header and/or value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers->add($name, $value);

        return $clone;
    }

    /**
     * Creates a new instance, without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return self
     */
    public function withoutHeader($name)
    {
        $clone = clone $this;
        $clone->headers->remove($name);

        return $clone;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Create a new instance, with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return self
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    /**
     * Extends MessageInterface::getHeaders() to provide request-specific
     * behavior.
     *
     * Retrieves all message headers.
     *
     * This method acts exactly like MessageInterface::getHeaders(), with one
     * behavioral change: if the Host header has not been previously set, the
     * method MUST attempt to pull the host segment of the composed URI, if
     * present.
     *
     * @see MessageInterface::getHeaders()
     * @see UriInterface::getHost()
     * @return array Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings.
     */
    public function getHeaders()
    {
        return $this->headers->all();
    }

    /**
     * Extends MessageInterface::getHeader() to provide request-specific
     * behavior.
     *
     * This method acts exactly like MessageInterface::getHeader(), with
     * one behavioral change: if the Host header is requested, but has
     * not been previously set, the method MUST attempt to pull the host
     * segment of the composed URI, if present.
     *
     * @see MessageInterface::getHeader()
     * @see UriInterface::getHost()
     * @param string $name Case-insensitive header field name.
     * @return string
     */
    public function getHeader($name)
    {
        return implode(',', $this->headers->get($name));
    }

    /**
     * Extends MessageInterface::getHeaderLines() to provide request-specific
     * behavior.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * This method acts exactly like MessageInterface::getHeaderLines(), with
     * one behavioral change: if the Host header is requested, but has
     * not been previously set, the method MUST attempt to pull the host
     * component of the composed URI, if present.
     *
     * @see MessageInterface::getHeaderLine()
     * @see UriInterface::getHost()
     * @param string $name Case-insensitive header field name.
     * @return string|null A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return a null value.
     */
    public function getHeaderLine($name)
    {
        return $this->headers->get($name);
    }

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        if ($this->requestTarget) {
            return $this->requestTarget;
        }

        if ($this->uri === null) {
            return '/';
        }

        $path = $this->uri->getPath();
        $query = $this->uri->getQuery();
        if ($query) {
            $path .= '?' . $query;
        }

        $this->requestTarget = $path;

        return $this->requestTarget;
    }

    /**
     * Create a new instance with a specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-2.7 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return self
     */
    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new \InvalidArgumentException(
                'Invalid request target provided; must be a string and cannot contain whitespace'
            );
        }

        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        $overrideMethod = $this->getHeader('X-Http-Method-Override');

        return $overrideMethod ? $overrideMethod : $this->method;
    }

    /**
     * Create a new instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * changed request method.
     *
     * @param string $method Case-insensitive method.
     * @return self
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request, if any.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method will update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header will be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, the returned request will not update the Host header of the
     * returned message -- even if the message contains no Host header. This
     * means that a call to `getHeader('Host')` on the original request MUST
     * equal the return value of a call to `getHeader('Host')` on the returned
     * request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return self
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        // Preserve previous host information if omitted from the new uri or if $preserveHost is true
        if (!strlen($uri->getHost()) || $preserveHost) {
            // TODO: Pass password? We have no easy way to retrieve it as it's not part of the UriInterface
            // This wasn't a problem when cloning, but due to preserving host requirement this is now a problem
            $uri = new Uri(
                $uri->getScheme(),
                $uri->getHost(),
                $uri->getPort(),
                $uri->getPath(),
                $uri->getQuery(),
                $uri->getFragment(),
                $uri->getUserInfo()
            );
        }

        $clone = clone $this;
        $clone->uri = $uri;

        return $clone;
    }

    /**
     * Get the Query String parameters.
     *
     * @return Bag
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * Retrieve all parameters for this request.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters->all();
    }

    /**
     * Get a parameter of this request.
     *
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getParameter($key, $default = null)
    {
        return $this->parameters->get($key, $default);
    }

    /**
     * Check if the request has a parameter with the specified key.
     *
     * @param $key
     * @return bool
     */
    public function hasParameter($key)
    {
        return $this->parameters->has($key);
    }

    /**
     * Return a new Request instance with an additional specified parameter.
     *
     * @param $key
     * @param $value
     * @return Request
     */
    public function withParameter($key, $value)
    {
        $clone = clone $this;
        $clone->parameters->add($key, $value);

        return $clone;
    }

    /**
     * Return a new Request instance with a replaced, new set of parameters.
     *
     * @param $parameters
     * @return Request
     */
    public function withParameters($parameters)
    {
        $clone = clone $this;
        $clone->parameters = new Bag($parameters);

        return $clone;
    }

    /**
     * Return a new Request instance without the specified parameter.
     *
     * @param $key
     * @return Request
     */
    public function withoutParameter($key)
    {
        $clone = clone $this;
        $clone->parameters->remove($key);

        return $clone;
    }
}
