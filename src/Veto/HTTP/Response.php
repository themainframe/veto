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
 * Response
 *
 * @since 0.1
 */
class Response
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var int
     */
    private $statusCode = 200;

    /**
     * @var string
     */
    private $statusText = 'OK';

    /**
     * @var string
     */
    private $version = '1.1';

    /**
     * @var Bag
     */
    public $headers;

    /**
     * Initialise the object
     *
     * @param mixed $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
        $this->headers = new Bag;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->content;
    }

    /**
     * Send headers and content
     *
     * @return $this
     */
    public function send()
    {
        // Already sent headers?
        if(headers_sent()) {
            return $this;
        }

        // Send status
        header('HTTP/' . $this->version . ' ' . $this->statusCode .
            $this->statusText, true, $this->statusCode);

        // Send headers
        foreach($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }

        // Send content
        echo (string)$this->content;

        return $this;
    }

    /**
     * @param int $statusCode
     * @param string $statusText
     * @return $this
     */
    public function setStatus($statusCode, $statusText = '')
    {
        $this->statusCode = $statusCode;
        $this->statusText = $statusText;
        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string $statusText
     * @return $this
     */
    public function setStatusText($statusText)
    {
        $this->statusText = $statusText;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
        return $this->statusText;
    }

    /**
     * @param string $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}
