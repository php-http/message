<?php

declare(strict_types=1);

namespace Http\Message\Tests\Fixtures;

use Psr\Http\Message\StreamInterface;

/**
 * Minimal in-memory stream with a configurable read chunk size.
 */
final class MemoryStream implements StreamInterface
{
    /**
     * @var resource|null
     */
    private $resource;

    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $chunkSize;

    public function __construct(string $body = '', int $chunkSize = 8192)
    {
        $resource = fopen('php://memory', 'rw+');
        if (false === $resource) {
            throw new \RuntimeException('Unable to open php://memory');
        }

        stream_set_read_buffer($resource, $chunkSize);
        stream_set_chunk_size($resource, $chunkSize);
        fwrite($resource, $body);
        fseek($resource, 0);

        $this->resource = $resource;
        $this->size = strlen($body);
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
        return (int) ftell($this->resource);
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
        return (int) fwrite($this->resource, $string);
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function read(int $length): string
    {
        return (string) fread($this->resource, min($this->chunkSize, $length));
    }

    public function getContents(): string
    {
        $this->rewind();

        return $this->read($this->size);
    }

    public function getMetadata(?string $key = null)
    {
        $metadata = stream_get_meta_data($this->resource);

        if (null === $key) {
            return $metadata;
        }

        return $metadata[$key] ?? null;
    }
}
