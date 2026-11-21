<?php

declare(strict_types=1);

namespace Http\Message\Tests\Decorator;

use Http\Message\Decorator\StreamDecorator;
use Http\Message\Tests\Fixtures\StreamDecoratorStub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

final class StreamDecoratorTest extends TestCase
{
    public function testUsesStreamDecoratorTrait(): void
    {
        self::assertContains(StreamDecorator::class, class_uses(StreamDecoratorStub::class));
    }

    public function testCastsStreamToString(): void
    {
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('__toString')->willReturn('body');

        self::assertSame('body', (string) new StreamDecoratorStub($stream));
    }

    public function testClosesStream(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())->method('close');

        (new StreamDecoratorStub($stream))->close();
    }

    public function testDetachesStream(): void
    {
        $resource = fopen('php://memory', 'r');
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('detach')->willReturn($resource);

        self::assertSame($resource, (new StreamDecoratorStub($stream))->detach());
    }

    public function testReturnsSize(): void
    {
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('getSize')->willReturn(1234);

        self::assertSame(1234, (new StreamDecoratorStub($stream))->getSize());
    }

    public function testReturnsCurrentPosition(): void
    {
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('tell')->willReturn(0);

        self::assertSame(0, (new StreamDecoratorStub($stream))->tell());
    }

    public function testChecksEof(): void
    {
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('eof')->willReturn(false);

        self::assertFalse((new StreamDecoratorStub($stream))->eof());
    }

    public function testChecksSeekable(): void
    {
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('isSeekable')->willReturn(true);

        self::assertTrue((new StreamDecoratorStub($stream))->isSeekable());
    }

    public function testSeeks(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())->method('seek')->with(0, SEEK_SET);

        (new StreamDecoratorStub($stream))->seek(0);
    }

    public function testRewinds(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())->method('rewind');

        (new StreamDecoratorStub($stream))->rewind();
    }

    public function testChecksWritable(): void
    {
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('isWritable')->willReturn(true);

        self::assertTrue((new StreamDecoratorStub($stream))->isWritable());
    }

    public function testWrites(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())->method('write')->with('body')->willReturn(4);

        self::assertSame(4, (new StreamDecoratorStub($stream))->write('body'));
    }

    public function testChecksReadable(): void
    {
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('isReadable')->willReturn(true);

        self::assertTrue((new StreamDecoratorStub($stream))->isReadable());
    }

    public function testReads(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())->method('read')->with(4)->willReturn('body');

        self::assertSame('body', (new StreamDecoratorStub($stream))->read(4));
    }

    public function testReturnsContents(): void
    {
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('getContents')->willReturn('body');

        self::assertSame('body', (new StreamDecoratorStub($stream))->getContents());
    }

    public function testReturnsMetadata(): void
    {
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('getMetadata')->willReturnMap([
            [null, ['key' => 'value']],
            ['key', 'value'],
            ['key2', null],
        ]);

        $decorator = new StreamDecoratorStub($stream);

        self::assertSame(['key' => 'value'], $decorator->getMetadata());
        self::assertSame('value', $decorator->getMetadata('key'));
        self::assertNull($decorator->getMetadata('key2'));
    }
}
