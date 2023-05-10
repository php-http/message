<?php

namespace spec\Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;

class MemoryStream implements StreamInterface
{
    private $resource;

    private $size = 0;

    private $chunkSize;

    public function __construct($body = '', $chunkSize = 8192)
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

    public function __toString(): string
    {
        return $this->getContents();
    }

    public function close(): void
    {
        fclose($this->resource);
    }

    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;

        return $resource;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function tell(): int
    {
        return ftell($this->resource);
    }

    public function eof(): bool
    {
        return feof($this->resource);
    }

    public function isSeekable(): bool
    {
        return true;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        fseek($this->resource, $offset, $whence);
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return true;
    }

    public function write(string $string): int
    {
        return fwrite($this->resource, $string);
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function read(int $length): string
    {
        return fread($this->resource, min($this->chunkSize, $length));
    }

    public function getContents(): string
    {
        $this->rewind();

        return $this->read($this->size);
    }

    public function getMetadata(string $key = null)
    {
        $metadata = stream_get_meta_data($this->resource);

        if (null === $key) {
            return $metadata;
        }

        return $metadata[$key];
    }
}
