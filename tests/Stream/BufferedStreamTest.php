<?php

declare(strict_types=1);

namespace Http\Message\Tests\Stream;

use Http\Message\Stream\BufferedStream;
use Http\Message\Tests\Fixtures\MemoryStream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

final class BufferedStreamTest extends TestCase
{
    public function testIsCastableToString(): void
    {
        self::assertSame('foo', (string) new BufferedStream($this->createNonSeekableStream('foo')));
    }

    public function testDetaches(): void
    {
        $inner = $this->createMock(StreamInterface::class);
        $inner->method('getSize')->willReturn(null);
        $inner->method('eof')->willReturn(true);
        $inner->expects($this->once())->method('close');

        $stream = new BufferedStream($inner);

        self::assertIsResource($stream->detach());
        self::assertNull($stream->detach());
        self::assertFalse($stream->isSeekable());
        self::assertFalse($stream->isReadable());
        self::assertNull($stream->getSize());
        self::assertSame([], $stream->getMetadata());
    }

    public function testGetsSizeOnceUnderlyingStreamIsConsumed(): void
    {
        $stream = new BufferedStream($this->createNonSeekableStream('foo'));

        self::assertNull($stream->getSize());
        self::assertSame('foo', $stream->getContents());
        self::assertSame(3, $stream->getSize());
    }

    public function testUsesKnownSizeOfUnderlyingStream(): void
    {
        self::assertSame(3, (new BufferedStream(new MemoryStream('foo')))->getSize());
    }

    public function testTells(): void
    {
        $stream = new BufferedStream($this->createNonSeekableStream('foo'));

        self::assertSame(0, $stream->tell());
        self::assertSame('foo', $stream->getContents());
        self::assertSame(3, $stream->tell());
    }

    public function testEof(): void
    {
        $stream = new BufferedStream($this->createNonSeekableStream('foo'));

        self::assertFalse($stream->eof());
        self::assertSame('foo', $stream->getContents());
        self::assertTrue($stream->eof());

        // underlying stream is at eof, but the buffer is not
        $stream->seek(0);
        self::assertFalse($stream->eof());
    }

    public function testIsSeekable(): void
    {
        self::assertTrue((new BufferedStream($this->createNonSeekableStream('')))->isSeekable());
    }

    public function testSeeks(): void
    {
        $stream = new BufferedStream($this->createNonSeekableStream('foo'));

        $stream->seek(0);
        self::assertSame(0, $stream->tell());

        self::assertSame('foo', $stream->getContents());
        $stream->seek(2);
        self::assertSame(2, $stream->tell());
        self::assertSame('o', $stream->read(1));
    }

    public function testRewinds(): void
    {
        $stream = new BufferedStream($this->createNonSeekableStream('foo'));

        $stream->rewind();
        self::assertSame(0, $stream->tell());

        self::assertSame('foo', $stream->getContents());
        self::assertSame(3, $stream->tell());

        $stream->rewind();
        self::assertSame(0, $stream->tell());
        self::assertSame('foo', $stream->getContents());
    }

    public function testIsNotWritable(): void
    {
        $stream = new BufferedStream($this->createNonSeekableStream(''));

        self::assertFalse($stream->isWritable());

        $this->expectException(\RuntimeException::class);
        $stream->write('foo');
    }

    public function testIsReadable(): void
    {
        self::assertTrue((new BufferedStream($this->createNonSeekableStream('')))->isReadable());
    }

    public function testReadsFromBufferAndUnderlyingStream(): void
    {
        $stream = new BufferedStream($this->createNonSeekableStream('foobarbaz'));

        self::assertSame('foo', $stream->read(3));
        self::assertSame('bar', $stream->read(3));

        $stream->rewind();
        self::assertSame('foob', $stream->read(4));
        self::assertSame('arbaz', $stream->read(5));
    }

    public function testReadsZeroBytes(): void
    {
        self::assertSame('', (new BufferedStream($this->createNonSeekableStream('foo')))->read(0));
    }

    public function testGetsContents(): void
    {
        $stream = new BufferedStream($this->createNonSeekableStream('foo'));

        self::assertSame('foo', $stream->getContents());
        self::assertTrue($stream->eof());
    }

    public function testGetsMetadata(): void
    {
        $stream = new BufferedStream($this->createNonSeekableStream(''));

        self::assertIsArray($stream->getMetadata());
        self::assertNull($stream->getMetadata('unexistant'));
        self::assertSame('TEMP', $stream->getMetadata('stream_type'));
    }

    public function testUsesMemoryBufferWhenFileBufferIsDisabled(): void
    {
        self::assertSame('MEMORY', (new BufferedStream($this->createNonSeekableStream(''), false))->getMetadata('stream_type'));
    }

    /**
     * @return StreamInterface a stream of unknown size that can only be read forward
     */
    private function createNonSeekableStream(string $content): StreamInterface
    {
        $position = 0;

        $stream = $this->createStub(StreamInterface::class);
        $stream->method('getSize')->willReturn(null);
        $stream->method('isSeekable')->willReturn(false);
        $stream->method('eof')->willReturnCallback(static function () use (&$position, $content): bool {
            return $position >= strlen($content);
        });
        $stream->method('read')->willReturnCallback(static function (int $length) use (&$position, $content): string {
            $chunk = (string) substr($content, $position, $length);
            $position += strlen($chunk);

            return $chunk;
        });

        return $stream;
    }
}
