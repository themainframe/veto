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

use Veto\Collection\Bag;
use Veto\Layer\Passable;

/**
 * Request
 * @since 0.1
 */
class Request extends Passable
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var Bag
     */
    public $parameters;

    /**
     * @var Bag
     */
    public $request;

    /**
     * The server variables.
     *
     * @var Bag
     */
    public $server;

    /**
     * @var Bag
     */
    public $query;

    /**
     * @var string
     */
    private $token;

    /**
     * Initialise the object.
     */
    public function __construct()
    {
        $this->token = substr(uniqid(), -6);
        $this->parameters = new Bag();
        $this->query = new Bag();
        $this->request = new Bag();
        $this->server = new Bag();
    }

    /**
     * Populate this object with values from the global scope.
     *
     * @return $this
     */
    public function initWithGlobals()
    {
        // Select request type
        $this->method = $_SERVER['REQUEST_METHOD'];

        // Store query string
        foreach ($_GET as $key => $value) {
            $this->query->add($key, $value);
        }

        // Store request parameters
        foreach ($_POST as $key => $value) {
            $this->request->add($key, $value);
        }

        // Store the server variables
        foreach ($_SERVER as $key => $value) {
            $this->server->add($key, $value);
        }

        $this->baseUrl = $this->determineBaseUrl();
        $this->uri = substr_replace(
            $this->server->get('REQUEST_URI'),
            '',
            0,
            strlen($this->baseUrl)
        );

        return $this;
    }

    /**
     * Determine the Base URL of the request.
     *
     * @todo Massive assumptions made about platform here. Review.
     * @return string
     */
    private function determineBaseUrl()
    {
        // Get the filename of the current script
        $basePath = $this->server->get('SCRIPT_NAME');
        $requestUrl = $this->server->get('REQUEST_URI');
        $baseUrl = '';

        // Trim both strings to the shortest length
        $length = min(strlen($basePath), strlen($requestUrl));
        $basePath = substr($basePath, 0, $length);
        $requestUrl = substr($requestUrl, 0, $length);

        // The common characters of the basePath and requestUrl are the baseUrl
        for ($c = 0; $c < strlen($basePath); $c ++) {

            if ($basePath[$c] != $requestUrl[$c]) {
               break;
            }

            $baseUrl .= $basePath[$c];
        }

        // Remove the trailing / if it exists
        if (substr($baseUrl, -1) == '/')
        {
            $baseUrl = substr($baseUrl, 0, -1);
        }

        return $baseUrl;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $uri
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}
