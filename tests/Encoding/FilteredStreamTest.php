<?php

declare(strict_types=1);

namespace Http\Message\Tests\Encoding;

use Http\Message\Tests\Fixtures\FilteredStreamStub;
use Http\Message\Tests\Fixtures\MemoryStream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

final class FilteredStreamTest extends TestCase
{
    public function testThrowsWithInvalidReadFilterOptions(): void
    {
        $this->expectException(\RuntimeException::class);

        new FilteredStreamStub($this->createStub(StreamInterface::class), 'foo');
    }

    public function testThrowsWithInvalidWriteFilterOptions(): void
    {
        $this->expectException(\RuntimeException::class);

        // the $writeFilterOptions argument is deprecated and triggers a silenced E_USER_DEPRECATED
        new FilteredStreamStub($this->createStub(StreamInterface::class), null, 'foo');
    }

    public function testReads(): void
    {
        $stream = new FilteredStreamStub(new MemoryStream('This is a test stream'));

        self::assertSame('VGhp', $stream->read(4));
    }

    public function testGetsContent(): void
    {
        $stream = new FilteredStreamStub(new MemoryStream('This is a test stream'));

        self::assertSame('VGhpcyBpcyBhIHRlc3Qgc3RyZWFt', $stream->getContents());
    }

    public function testDoesNotKnowContentSize(): void
    {
        self::assertNull((new FilteredStreamStub(new MemoryStream('This is a test stream')))->getSize());
    }

    public function testIsNotSeekable(): void
    {
        self::assertFalse((new FilteredStreamStub(new MemoryStream('This is a test stream')))->isSeekable());
    }

    public function testCastsToFilteredString(): void
    {
        self::assertSame('VGhpcyBpcyBhIHRlc3Qgc3RyZWFt', (string) new FilteredStreamStub(new MemoryStream('This is a test stream')));
    }
}
