<?php

namespace Http\Message;

use Psr\Http\Message\StreamInterface;

/**
 * Represent a stream cloned into memory.
 */
class MemoryClonedStream implements StreamInterface
{
    const COPY_BUFFER = 8192;

    private $resource;

    private $size;

    /**
     * @param StreamInterface $stream        Stream to clone
     * @param bool            $useFileBuffer Use a file buffer to avoid memory consumption on PHP script (default to true)
     * @param int             $memoryBuffer  The amount of memory of which the stream is buffer into a file when setting
     *                                       $useFileBuffer to true (default to 2MB)
     */
    public function __construct(StreamInterface $stream, $useFileBuffer = true, $memoryBuffer = 2097152)
    {
        $this->size = 0;

        if ($useFileBuffer) {
            $this->resource = fopen('php://temp/maxmemory:'.$memoryBuffer, 'rw+');
        } else {
            $this->resource = fopen('php://memory', 'rw+');
        }

        $position = null;

        if ($stream->isSeekable()) {
            $position = $stream->tell();
            $stream->rewind();
        }

        while (!$stream->eof()) {
            $this->size += fwrite($this->resource, $stream->read(self::COPY_BUFFER));
        }

        if ($stream->isSeekable()) {
            $stream->seek($position);
        }

        rewind($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        fclose($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        return ftell($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return feof($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->resource, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return rewind($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        return fwrite($this->resource, $string);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        return fread($this->resource, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        $this->rewind();

        return $this->read($this->size);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        $metadata = stream_get_meta_data($this->resource);

        if (null === $key) {
            return $metadata;
        }

        return $metadata[$key];
    }
}
