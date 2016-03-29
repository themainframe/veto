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
namespace Veto\Http;

use Psr\Http\Message\StreamInterface;

/**
 * A HTTP Message Body, according to PSR-7.
 *
 * @since 0.1
 */
class MessageBody implements StreamInterface
{
    /**
     * Modes in which a stream is readable
     *
     * @var array
     * @link http://php.net/manual/function.fopen.php
     */
    protected static $readableModes = array('r', 'r+', 'w+', 'a+', 'x+', 'c+');

    /**
     * Modes in which a stream is writable
     *
     * @var array
     * @link http://php.net/manual/function.fopen.php
     */
    protected static $writableModes = array('r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+');

    /**
     * The stream underlying the message body
     *
     * @var resource
     */
    protected $stream;

    /**
     * The underlying stream's metadata
     *
     * @var null|array
     */
    protected $metadata;

    /**
     * Is the underlying stream readable?
     *
     * @var bool
     */
    protected $readable;

    /**
     * Is the underlying stream writable?
     *
     * @var bool
     */
    protected $writable;

    /**
     * Is the underlying stream seekable?
     *
     * @var bool
     */
    protected $seekable;

    /**
     * Create a new message body with the provided stream underlying it.
     *
     * @param resource $stream
     */
    public function __construct($stream)
    {
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException(
                '\Veto\Http\Body::__construct() argument must be a PHP stream resource'
            );
        }

        $this->attach($stream);
    }

    /**
     * Attach a resource to this message body.
     *
     * @param resource $stream
     */
    public function attach($stream)
    {
        if (false === is_resource($stream)) {
            throw new \InvalidArgumentException(
                '\Veto\Http\Body::attach() argument must be a PHP stream resource'
            );
        }

        // If we are already attached, detach first
        if (true === $this->isAttached()) {
            $this->detach();
        }

        $this->stream = $stream;
        $this->setMetadata($stream);
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $stream = $this->stream;
        $this->stream = null;

        return $stream;
    }

    /**
     * Check if a stream resource is already attached to this message body.
     *
     * @return bool
     */
    public function isAttached()
    {
        return is_resource($this->stream);
    }

    /**
     * Set the metadata state information on this object, sourced from the stream metadata.
     *
     * @param resource $stream
     */
    protected function setMetadata($stream)
    {
        $this->metadata = stream_get_meta_data($stream);

        // Check for readable modes
        $this->readable = false;
        foreach (self::$readableModes as $mode) {
            if (strpos($this->metadata['mode'], $mode) === 0) {
                $this->readable = true;
                break;
            }
        }

        // Check for writable modes
        $this->writable = false;
        foreach (self::$writableModes as $mode) {
            if (strpos($this->metadata['mode'], $mode) === 0) {
                $this->writable = true;
                break;
            }
        }

        // Is the underlying stream seekable?
        $this->seekable = $this->metadata['seekable'];
    }

    /**
     * Get the size of the stream if known
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if (true === $this->isAttached()) {
            $stats = fstat($this->stream);
            return isset($stats['size']) ? $stats['size'] : null;
        }

        return null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int|bool Position of the file pointer or false on error.
     */
    public function tell()
    {
        return $this->isAttached() ? ftell($this->stream) : false;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return $this->isAttached() ? feof($this->stream) : true;
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this->isAttached() && $this->seekable;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return $this->isAttached() && $this->isSeekable() ?
            fseek($this->stream, $offset, $whence) :
            false;
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will return FALSE, indicating
     * failure; otherwise, it will perform a seek(0), and return the status of
     * that operation.
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function rewind()
    {
        return $this->isAttached() && $this->isSeekable() ? rewind($this->stream) : false;
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        return is_null($this->writable) ? false : $this->writable;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int|bool Returns the number of bytes written to the stream on
     *     success or FALSE on failure.
     */
    public function write($string)
    {
        return $this->isAttached() && $this->isWritable() ? fwrite($this->stream, $string) : false;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        return is_null($this->readable) ? false : $this->readable;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string|false Returns the data read from the stream, false if
     *     unable to read or if an error occurs.
     */
    public function read($length)
    {
        return $this->isAttached() && $this->isReadable() ? fread($this->stream, $length) : false;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     */
    public function getContents()
    {
        return $this->isAttached() && $this->isReadable() ? stream_get_contents($this->stream) : '';
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if (true === is_null($key)) {
            return $this->metadata;
        }

        return isset($this->metadata[$key]) ? $this->metadata[$key] : null;
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        fclose($this->stream);
    }

    /**
     * Read the entire contents of the message body into a PHP string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->isAttached() ? stream_get_contents($this->stream, -1, 0) : '';
    }
}
