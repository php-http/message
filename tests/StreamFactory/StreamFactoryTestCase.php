<?php

declare(strict_types=1);

namespace Http\Message\Tests\StreamFactory;

use Http\Message\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

abstract class StreamFactoryTestCase extends TestCase
{
    abstract protected function createFactory(): StreamFactory;

    /**
     * Creates a stream with the PSR-7 implementation of the factory under test.
     *
     * @param resource $resource
     */
    abstract protected function createNativeStream($resource): StreamInterface;

    public function testCreatesStreamFromString(): void
    {
        $stream = $this->createFactory()->createStream('foo');

        self::assertSame('foo', (string) $stream);
    }

    public function testCreatesStreamFromNull(): void
    {
        $stream = $this->createFactory()->createStream(null);

        self::assertSame('', (string) $stream);
    }

    public function testCreatesStreamFromResource(): void
    {
        $resource = $this->openMemoryResource('foo');

        self::assertSame('foo', (string) $this->createFactory()->createStream($resource));
    }

    public function testCreatesStreamFromStream(): void
    {
        $stream = $this->createNativeStream($this->openMemoryResource('foo'));

        self::assertSame($stream, $this->createFactory()->createStream($stream));
    }

    public function testCreatesStreamFromNonSeekableResource(): void
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            self::markTestSkipped('Unix domain socket pairs are not available on Windows.');
        }

        $pair = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        self::assertIsArray($pair);
        [$reader, $writer] = $pair;
        fwrite($writer, 'foo');
        fclose($writer);

        $stream = $this->createFactory()->createStream($reader);

        self::assertFalse($stream->isSeekable());
        self::assertSame('foo', $stream->getContents());
    }

    public function testDoesNotRewindExistingStream(): void
    {
        $stream = $this->createNativeStream($this->openMemoryResource('abcdef'));
        $stream->seek(3);

        self::assertSame('def', $this->createFactory()->createStream($stream)->getContents());
    }

    public function testDoesNotRewindExistingResource(): void
    {
        $resource = $this->openMemoryResource('abcdef');
        fseek($resource, 3);

        self::assertSame('def', $this->createFactory()->createStream($resource)->getContents());
    }

    /**
     * @return resource
     */
    private function openMemoryResource(string $content)
    {
        $resource = fopen('php://memory', 'rw+');
        self::assertIsResource($resource);
        fwrite($resource, $content);
        rewind($resource);

        return $resource;
    }
}
