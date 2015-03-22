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
 * HeaderBag - a Bag of HTTP headers
 */
class HeaderBag extends Bag
{
    /**
     * Special HTTP headers that do not have the "HTTP_" prefix, for some reason.
     *
     * @var array
     */
    protected static $special = array(
        'CONTENT_TYPE',
        'CONTENT_LENGTH',
        'PHP_AUTH_USER',
        'PHP_AUTH_PW',
        'PHP_AUTH_DIGEST',
        'AUTH_TYPE',
    );

    /**
     * Create a new HeaderBag, derived from the provided environment bag.
     *
     * @param Bag $environment The environment variables
     * @return self
     */
    public static function createFromEnvironment(Bag $environment)
    {
        $headers = new static();

        foreach ($environment as $key => $value) {
            $key = strtoupper($key);
            if (strpos($key, 'HTTP_') === 0 || in_array($key, static::$special)) {
                if ($key === 'HTTP_CONTENT_TYPE' || $key === 'HTTP_CONTENT_LENGTH') {
                    continue;
                }
                $headers->add($key, $value);
            }
        }

        return $headers;
    }

    /**
     * Add a header to the bag
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function add($key, $value)
    {
        $header = $this->get($key, array());

        if (is_array($value)) {
            $header = array_merge($header, $value);
        } else {
            $header[] = $value;
        }

        parent::add($key, $header);

        return $this;
    }

    /**
     * Get a header from the bag by key
     *
     * @param mixed $key
     * @param array $default The default value if no header matches
     * @return array
     */
    public function get($key, $default = array())
    {
        return parent::get($this->normalizeKey($key), $default);
    }

    /**
     * Check if the bag contains a given key
     *
     * @param mixed $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * If the bag contains the given key, return the value, then remove it from the bag.
     *
     * @param $key
     * @return mixed|null
     */
    public function remove($key)
    {
        return parent::remove($this->normalizeKey($key));
    }

    /**
     * Get the underlying array of the contents of the bag.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Normalize header name, converting it to lower-case
     *
     * @param  string $key The case-insensitive header name
     * @return string Normalized header name
     */
    public function normalizeKey($key)
    {
        $key = strtolower($key);
        $key = str_replace(array('-', '-'), ' ', $key);
        $key = ucwords($key);
        $key = str_replace(' ', '-', $key);

        return $key;
    }
}
