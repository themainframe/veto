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

/**
 * Request
 * @since 0.1
 */
class Request
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var Bag
     */
    public $parameters;

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
    }

    /**
     * Populate this object with values from the global scope.
     *
     * @return $this
     */
    public function initWithGlobals()
    {
        // Select request type
        $this->type = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];

        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
}
