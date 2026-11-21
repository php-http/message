<?php

declare(strict_types=1);

namespace Http\Message\Tests\Encoding;

use Http\Message\Encoding\DechunkStream;
use Http\Message\Tests\Fixtures\MemoryStream;
use PHPUnit\Framework\TestCase;

final class DechunkStreamTest extends TestCase
{
    public function testReads(): void
    {
        $stream = new DechunkStream(new MemoryStream("4\r\ntest\r\n4\r\ntest\r\n0\r\n\r\n\0"));

        self::assertSame('testte', $stream->read(6));
        self::assertSame('st', $stream->read(6));
    }

    public function testGetsContent(): void
    {
        $stream = new DechunkStream(new MemoryStream("4\r\ntest\r\n0\r\n\r\n\0"));

        self::assertSame('test', $stream->getContents());
    }

    public function testDoesNotKnowContentSize(): void
    {
        self::assertNull((new DechunkStream(new MemoryStream("4\r\ntest\r\n0\r\n\r\n\0")))->getSize());
    }
}
