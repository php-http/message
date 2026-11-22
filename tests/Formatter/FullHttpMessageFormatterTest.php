<?php

declare(strict_types=1);

namespace Http\Message\Tests\Formatter;

use Http\Message\Formatter;
use Http\Message\Formatter\FullHttpMessageFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class FullHttpMessageFormatterTest extends TestCase
{
    private const HEADERS = [
        'X-Param-Foo' => ['foo'],
        'X-Param-Bar' => ['bar'],
    ];

    public function testIsAFormatter(): void
    {
        self::assertInstanceOf(Formatter::class, new FullHttpMessageFormatter());
    }

    /**
     * @return iterable<string, array{int|null, string}>
     */
    public static function bodyLengthProvider(): iterable
    {
        yield 'with size limit' => [18, 'This is an HTML st'];
        yield 'without size limit' => [null, 'This is an HTML stream content.'];
    }

    /**
     * @dataProvider bodyLengthProvider
     */
    #[DataProvider('bodyLengthProvider')]
    public function testFormatsRequest(?int $maxBodyLength, string $expectedBody): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('isSeekable')->willReturn(true);
        $stream->method('__toString')->willReturn('This is an HTML stream content.');
        $stream->expects($this->once())->method('rewind');

        $expected = "GET /foo HTTP/1.1\nX-Param-Foo: foo\nX-Param-Bar: bar\n\n".$expectedBody;

        self::assertSame($expected, (new FullHttpMessageFormatter($maxBodyLength))->formatRequest($this->createRequestStub($stream, self::HEADERS)));
    }

    /**
     * @dataProvider bodyLengthProvider
     */
    #[DataProvider('bodyLengthProvider')]
    public function testFormatsResponse(?int $maxBodyLength, string $expectedBody): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('isSeekable')->willReturn(true);
        $stream->method('__toString')->willReturn('This is an HTML stream content.');
        $stream->expects($this->exactly(2))->method('rewind');

        $response = $this->createResponseStub($stream);
        $formatter = new FullHttpMessageFormatter($maxBodyLength);
        $expected = "HTTP/1.1 200 OK\nX-Param-Foo: foo\nX-Param-Bar: bar\n\n".$expectedBody;

        self::assertSame($expected, $formatter->formatResponse($response));
        self::assertSame($expected, $formatter->formatResponseForRequest($response, $this->createStub(RequestInterface::class)));
    }

    /**
     * @return iterable<string, array{int, bool}>
     */
    public static function unreadableBodyProvider(): iterable
    {
        yield 'body length is zero' => [0, true];
        yield 'stream is not seekable' => [1000, false];
    }

    /**
     * @dataProvider unreadableBodyProvider
     */
    #[DataProvider('unreadableBodyProvider')]
    public function testDoesNotReadRequestBody(int $maxBodyLength, bool $seekable): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('isSeekable')->willReturn($seekable);
        $stream->expects($this->never())->method('__toString');
        $stream->expects($this->never())->method('rewind');

        self::assertSame(
            "GET /foo HTTP/1.1\nX-Param-Foo: foo\nX-Param-Bar: bar\n\n",
            (new FullHttpMessageFormatter($maxBodyLength))->formatRequest($this->createRequestStub($stream, self::HEADERS))
        );
    }

    /**
     * @dataProvider unreadableBodyProvider
     */
    #[DataProvider('unreadableBodyProvider')]
    public function testDoesNotReadResponseBody(int $maxBodyLength, bool $seekable): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('isSeekable')->willReturn($seekable);
        $stream->expects($this->never())->method('__toString');
        $stream->expects($this->never())->method('rewind');

        self::assertSame(
            "HTTP/1.1 200 OK\nX-Param-Foo: foo\nX-Param-Bar: bar\n\n",
            (new FullHttpMessageFormatter($maxBodyLength))->formatResponse($this->createResponseStub($stream))
        );
    }

    public function testOmitsBinaryBody(): void
    {
        $stream = $this->createSeekableStream("\0");

        self::assertSame(
            "GET /foo HTTP/1.1\n\n[binary stream omitted]",
            (new FullHttpMessageFormatter(1))->formatRequest($this->createRequestStub($stream, []))
        );
    }

    public function testAllowsCustomBinaryDetection(): void
    {
        $stream = $this->createSeekableStream("\0");

        self::assertSame(
            "GET /foo HTTP/1.1\n\n\0",
            (new FullHttpMessageFormatter(1, '/\x01/'))->formatRequest($this->createRequestStub($stream, []))
        );
    }

    public function testKeepsLineBreaksInBody(): void
    {
        $stream = $this->createSeekableStream("foo\nbar");

        self::assertSame(
            "GET /foo HTTP/1.1\n\nfoo\nbar",
            (new FullHttpMessageFormatter(7))->formatRequest($this->createRequestStub($stream, []))
        );
    }

    public function testTruncatesMultibyteBodyByCharacters(): void
    {
        $stream = $this->createSeekableStream('Grüße aus Österreich');

        self::assertSame(
            "GET /foo HTTP/1.1\n\nGrüße",
            (new FullHttpMessageFormatter(5))->formatRequest($this->createRequestStub($stream, []))
        );
    }

    private function createSeekableStream(string $content): StreamInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('isSeekable')->willReturn(true);
        $stream->method('__toString')->willReturn($content);
        $stream->expects($this->once())->method('rewind');

        return $stream;
    }

    /**
     * @param array<string, list<string>> $headers
     */
    private function createRequestStub(StreamInterface $body, array $headers): RequestInterface
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getBody')->willReturn($body);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getRequestTarget')->willReturn('/foo');
        $request->method('getProtocolVersion')->willReturn('1.1');
        $request->method('getHeaders')->willReturn($headers);

        return $request;
    }

    private function createResponseStub(StreamInterface $body): ResponseInterface
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getBody')->willReturn($body);
        $response->method('getProtocolVersion')->willReturn('1.1');
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getReasonPhrase')->willReturn('OK');
        $response->method('getHeaders')->willReturn(self::HEADERS);

        return $response;
    }
}
