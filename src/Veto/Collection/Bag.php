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
namespace Veto\Collection;
use Traversable;

/**
 * Bag
 *
 * @since 0.1
 */
class Bag implements \IteratorAggregate
{
    /**
     * @var mixed[]
     */
    private $items;

    /**
     * Initialise the object
     */
    public function __construct()
    {
        $this->items = array();
    }

    /**
     * Add an item to the bag
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function add($key, $value)
    {
        $this->items[$key] = $value;
        return $this;
    }

    /**
     * Get an item from the bag by key
     *
     * @param mixed $key
     * @return mixed|null
     */
    public function get($key)
    {
        if(array_key_exists($key, $this->items)) {
            return $this->items[$key];
        } else {
            return null;
        }
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}
