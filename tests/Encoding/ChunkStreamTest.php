<?php

declare(strict_types=1);

namespace Http\Message\Tests\Encoding;

use Http\Message\Encoding\ChunkStream;
use Http\Message\Encoding\DechunkStream;
use Http\Message\Tests\Fixtures\MemoryStream;
use PHPUnit\Framework\TestCase;

final class ChunkStreamTest extends TestCase
{
    public function testChunksContent(): void
    {
        $stream = new ChunkStream(new MemoryStream('This is a stream'));

        self::assertSame("10\r\nThis is a stream\r\n0\r\n\r\n", $stream->getContents());
    }

    public function testChunksInMultipleParts(): void
    {
        $stream = new ChunkStream(new MemoryStream('This is a stream', 6));

        self::assertSame("6\r\nThis i\r\n6\r\ns a st\r\n4\r\nream\r\n0\r\n\r\n", $stream->getContents());
    }

    public function testDoesNotKnowContentSize(): void
    {
        self::assertNull((new ChunkStream(new MemoryStream('This is a stream')))->getSize());
    }

    public function testRoundTripsWithDechunkStream(): void
    {
        $chunked = (new ChunkStream(new MemoryStream('This is a stream', 6)))->getContents();

        self::assertSame('This is a stream', (new DechunkStream(new MemoryStream($chunked)))->getContents());
    }
}
