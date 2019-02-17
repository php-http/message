<?php

namespace spec\Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;

class MemoryStream implements StreamInterface
{
    private $resource;

    private $size = 0;

    private $chunkSize;

    public function __construct($body = "", $chunkSize = 8192)
    {
        $this->size = strlen($body);
        $this->resource = fopen('php://memory', 'rw+');

        if (null !== $chunkSize) {
            stream_set_read_buffer($this->resource, $chunkSize);
            stream_set_chunk_size($this->resource, $chunkSize);
        }

        fwrite($this->resource, $body);
        fseek($this->resource, 0);

        $this->chunkSize = $chunkSize;
    }

    public function __toString()
    {
        return $this->getContents();
    }

    public function close()
    {
        fclose($this->resource);
    }

    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;

        return $resource;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function tell()
    {
        return ftell($this->resource);
    }

    public function eof()
    {
        return feof($this->resource);
    }

    public function isSeekable()
    {
        return true;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        fseek($this->resource, $offset, $whence);
    }

    public function rewind()
    {
        $this->seek(0);
    }

    public function isWritable()
    {
        return true;
    }

    public function write($string)
    {
        fwrite($this->resource, $string);
    }

    public function isReadable()
    {
        return true;
    }

    public function read($length)
    {
        return fread($this->resource, min($this->chunkSize, $length));
    }

    public function getContents()
    {
        $this->rewind();

        return $this->read($this->size);
    }

    public function getMetadata($key = null)
    {
        $metadata = stream_get_meta_data($this->resource);

        if (null === $key) {
            return $metadata;
        }

        return $metadata[$key];
    }
}
