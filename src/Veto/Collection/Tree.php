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

/**
 * Tree
 *
 * @since 0.1
 */
class Tree implements \ArrayAccess
{
    protected $values = array();

    /**
     * Initialise a new tree object with an existing array of data.
     *
     * @param array $data The data to use
     */
    public function __construct($data = array())
    {
        $this->values = $data;
    }

    /**
     * Locates an object identified by a .-delimited path within the tree.
     *
     * @param string $path The path to locate within the tree.
     * @return array|null null if the object cannot be located.
     */
    public function get($path)
    {
        $pathParts = explode('.', $path);
        $pointer = &$this->values;

        foreach ($pathParts as $part) {
            if (array_key_exists($part, $pointer)) {
                $pointer = &$pointer[$part];
            } else {
                return null;
            }
        }

        return $pointer;
    }

    /**
     * Adds a value to the tree structure. The path must already exist and be an array type value.
     *
     * @param string $path The path to the position where the value should be added.
     * @param mixed $key The key to add to the tree.
     * @param mixed $value The value to add to the tree.
     * @return bool
     */
    public function add($path, $key, $value)
    {
        $pathParts = explode('.', $path);
        $pointer = &$this->values;

        foreach ($pathParts as $part) {
            if (array_key_exists($part, $pointer)) {
                $pointer = &$pointer[$part];
            } else {
                return false;
            }
        }

        if (!is_array($pointer)) {
            return false;
        }

        $pointer[$key] = $value;

        return true;
    }

    /**
     * Merge an array of data into this tree object.
     * The behaviour is identical to that of array_merge_recursive.
     *
     * @param array $data The data to merge into the tree.
     * @return bool
     */
    public function merge($data)
    {
        $this->values = array_merge_recursive(
            $this->values,
            $data
        );

        return true;
    }

    /**
     * Get the entire dataset managed by this Tree object.
     *
     * @return array
     */
    public function all()
    {
        return $this->values;
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->values);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }
}