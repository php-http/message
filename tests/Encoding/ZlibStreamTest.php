<?php

declare(strict_types=1);

namespace Http\Message\Tests\Encoding;

use Http\Message\Encoding\CompressStream;
use Http\Message\Encoding\DecompressStream;
use Http\Message\Encoding\DeflateStream;
use Http\Message\Encoding\GzipDecodeStream;
use Http\Message\Encoding\GzipEncodeStream;
use Http\Message\Encoding\InflateStream;
use Http\Message\Tests\Fixtures\MemoryStream;
use Http\Message\Tests\Fixtures\ZlibExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

/**
 * @requires extension zlib
 */
#[RequiresPhpExtension('zlib')]
final class ZlibStreamTest extends TestCase
{
    private const CONTENT = 'This is a test stream';

    protected function tearDown(): void
    {
        ZlibExtension::reset();
    }

    /**
     * @return iterable<string, array{class-string<StreamInterface>, callable(string): string}>
     */
    public static function encoderProvider(): iterable
    {
        yield 'compress (RFC 1950)' => [CompressStream::class, 'gzcompress'];
        yield 'deflate (RFC 1951)' => [DeflateStream::class, 'gzdeflate'];
        yield 'gzip encode (RFC 1952)' => [GzipEncodeStream::class, 'gzencode'];
    }

    /**
     * @return iterable<string, array{class-string<StreamInterface>, callable(string): string}>
     */
    public static function decoderProvider(): iterable
    {
        yield 'decompress (RFC 1950)' => [DecompressStream::class, 'gzcompress'];
        yield 'inflate (RFC 1951)' => [InflateStream::class, 'gzdeflate'];
        yield 'gzip decode (RFC 1952)' => [GzipDecodeStream::class, 'gzencode'];
    }

    /**
     * @return iterable<string, array{class-string<StreamInterface>, class-string<StreamInterface>}>
     */
    public static function roundTripProvider(): iterable
    {
        yield 'compress / decompress' => [CompressStream::class, DecompressStream::class];
        yield 'deflate / inflate' => [DeflateStream::class, InflateStream::class];
        yield 'gzip encode / gzip decode' => [GzipEncodeStream::class, GzipDecodeStream::class];
    }

    /**
     * @return iterable<string, array{class-string<StreamInterface>}>
     */
    public static function zlibDependentStreamProvider(): iterable
    {
        yield 'compress' => [CompressStream::class];
        yield 'decompress' => [DecompressStream::class];
        yield 'inflate' => [InflateStream::class];
        yield 'gzip encode' => [GzipEncodeStream::class];
        yield 'gzip decode' => [GzipDecodeStream::class];
    }

    /**
     * @param class-string<StreamInterface> $streamClass
     * @param callable(string): string      $encode
     *
     * @dataProvider encoderProvider
     */
    #[DataProvider('encoderProvider')]
    public function testEncoderReads(string $streamClass, callable $encode): void
    {
        $stream = new $streamClass(new MemoryStream(self::CONTENT));

        self::assertSame(substr($encode(self::CONTENT), 0, 4), $stream->read(4));
    }

    /**
     * @param class-string<StreamInterface> $streamClass
     * @param callable(string): string      $encode
     *
     * @dataProvider encoderProvider
     */
    #[DataProvider('encoderProvider')]
    public function testEncoderGetsContent(string $streamClass, callable $encode): void
    {
        $stream = new $streamClass(new MemoryStream(self::CONTENT));

        self::assertSame($encode(self::CONTENT), $stream->getContents());
    }

    /**
     * @param class-string<StreamInterface> $streamClass
     * @param callable(string): string      $encode
     *
     * @dataProvider decoderProvider
     */
    #[DataProvider('decoderProvider')]
    public function testDecoderReads(string $streamClass, callable $encode): void
    {
        $stream = new $streamClass(new MemoryStream($encode(self::CONTENT)));

        self::assertSame('This', $stream->read(4));
    }

    /**
     * @param class-string<StreamInterface> $streamClass
     * @param callable(string): string      $encode
     *
     * @dataProvider decoderProvider
     */
    #[DataProvider('decoderProvider')]
    public function testDecoderGetsContent(string $streamClass, callable $encode): void
    {
        $stream = new $streamClass(new MemoryStream($encode(self::CONTENT)));

        self::assertSame(self::CONTENT, $stream->getContents());
    }

    /**
     * @param class-string<StreamInterface> $encoderClass
     * @param class-string<StreamInterface> $decoderClass
     *
     * @dataProvider roundTripProvider
     */
    #[DataProvider('roundTripProvider')]
    public function testRoundTrip(string $encoderClass, string $decoderClass): void
    {
        $content = str_repeat(self::CONTENT, 1000);
        $encoded = (new $encoderClass(new MemoryStream($content)))->getContents();

        self::assertSame($content, (new $decoderClass(new MemoryStream($encoded)))->getContents());
    }

    /**
     * @param class-string<StreamInterface> $streamClass
     * @param callable(string): string      $encode
     *
     * @dataProvider encoderProvider
     * @dataProvider decoderProvider
     */
    #[DataProvider('encoderProvider')]
    #[DataProvider('decoderProvider')]
    public function testDoesNotKnowContentSize(string $streamClass, callable $encode): void
    {
        $stream = new $streamClass(new MemoryStream($encode(self::CONTENT)));

        self::assertNull($stream->getSize());
    }

    /**
     * @param class-string<StreamInterface> $streamClass
     *
     * @dataProvider zlibDependentStreamProvider
     */
    #[DataProvider('zlibDependentStreamProvider')]
    public function testThrowsWhenZlibIsNotLoaded(string $streamClass): void
    {
        ZlibExtension::disable();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The zlib extension must be enabled to use this stream');

        new $streamClass(new MemoryStream());
    }
}
