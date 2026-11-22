<?php

declare(strict_types=1);

namespace Http\Message\Tests\Formatter;

use GuzzleHttp\Psr7\Request;
use Http\Message\Formatter;
use Http\Message\Formatter\CurlCommandFormatter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

final class CurlCommandFormatterTest extends TestCase
{
    public function testIsAFormatter(): void
    {
        self::assertInstanceOf(Formatter::class, new CurlCommandFormatter());
    }

    public function testFormatsRequest(): void
    {
        $request = $this->createRequestStub('GET', '1.1', ['foo' => ['bar', 'baz']], $this->createStub(StreamInterface::class));
        $request->method('getHeaderLine')->willReturnMap([['foo', 'bar, baz']]);

        self::assertSame("curl 'http://foo.com/bar' -H 'foo: bar, baz'", (new CurlCommandFormatter())->formatRequest($request));
    }

    public function testStripsFragmentFromUri(): void
    {
        $request = new Request('GET', 'http://foo.com/bar#fragment');

        self::assertSame("curl 'http://foo.com/bar'", (new CurlCommandFormatter())->formatRequest($request));
    }

    public function testFormatsPostRequest(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('__toString')->willReturn('body " data'." test' bar");
        $body->method('getSize')->willReturn(1);
        $body->method('isSeekable')->willReturn(true);
        $body->expects($this->once())->method('rewind');

        $request = $this->createRequestStub('POST', '2.0', [], $body);

        self::assertSame(
            "curl 'http://foo.com/bar' --http2 --request POST --data 'body \" data test'\'' bar'",
            (new CurlCommandFormatter())->formatRequest($request)
        );
    }

    public function testFormatsHeadRequestWithHttp10(): void
    {
        $request = new Request('HEAD', 'http://foo.com/bar', [], null, '1.0');

        self::assertSame("curl 'http://foo.com/bar' --http1.0 --head", (new CurlCommandFormatter())->formatRequest($request));
    }

    public function testSkipsHostHeaderMatchingUri(): void
    {
        $request = new Request('GET', 'http://foo.com/bar', ['Host' => 'foo.com']);

        self::assertSame("curl 'http://foo.com/bar'", (new CurlCommandFormatter())->formatRequest($request));
    }

    public function testFormatsRequestWithUserAgent(): void
    {
        $request = $this->createRequestStub('GET', '1.1', ['user-agent' => ['foobar-browser']], $this->createStub(StreamInterface::class));

        self::assertSame("curl 'http://foo.com/bar' -A 'foobar-browser'", (new CurlCommandFormatter())->formatRequest($request));
    }

    public function testOmitsBinaryBody(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('__toString')->willReturn("\0");
        $body->method('getSize')->willReturn(1);
        $body->method('isSeekable')->willReturn(true);
        $body->expects($this->once())->method('rewind');

        $request = $this->createRequestStub('POST', '1.1', [], $body);

        self::assertSame(
            "curl 'http://foo.com/bar' --request POST --data '[binary stream omitted]'",
            (new CurlCommandFormatter())->formatRequest($request)
        );
    }

    public function testKeepsLineBreaksInBody(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('__toString')->willReturn("foo\nbar");
        $body->method('getSize')->willReturn(1);
        $body->method('isSeekable')->willReturn(true);
        $body->expects($this->once())->method('rewind');

        $request = $this->createRequestStub('POST', '1.1', [], $body);

        self::assertSame(
            "curl 'http://foo.com/bar' --request POST --data 'foo\nbar'",
            (new CurlCommandFormatter())->formatRequest($request)
        );
    }

    public function testOmitsNonSeekableBody(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('getSize')->willReturn(1);
        $body->method('isSeekable')->willReturn(false);
        $body->expects($this->never())->method('__toString');
        $body->expects($this->never())->method('rewind');

        $request = $this->createRequestStub('POST', '1.1', [], $body);

        self::assertSame(
            "curl 'http://foo.com/bar' --request POST --data '[non-seekable stream omitted]'",
            (new CurlCommandFormatter())->formatRequest($request)
        );
    }

    public function testOmitsTooLongBody(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('getSize')->willReturn(2097153);
        $body->method('isSeekable')->willReturn(true);
        $body->expects($this->never())->method('__toString');
        $body->expects($this->never())->method('rewind');

        $request = $this->createRequestStub('POST', '1.1', [], $body);

        self::assertSame(
            "curl 'http://foo.com/bar' --request POST --data '[too long stream omitted]'",
            (new CurlCommandFormatter())->formatRequest($request)
        );
    }

    public function testDoesNothingForResponse(): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $formatter = new CurlCommandFormatter();

        self::assertSame('', $formatter->formatResponse($response));
        self::assertSame('', $formatter->formatResponseForRequest($response, $this->createStub(RequestInterface::class)));
    }

    /**
     * @param array<string, list<string>> $headers
     *
     * @return RequestInterface&\PHPUnit\Framework\MockObject\Stub
     */
    private function createRequestStub(string $method, string $protocolVersion, array $headers, StreamInterface $body): RequestInterface
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('withFragment')->willReturnSelf();
        $uri->method('__toString')->willReturn('http://foo.com/bar');

        $request = $this->createStub(RequestInterface::class);
        $request->method('getUri')->willReturn($uri);
        $request->method('getBody')->willReturn($body);
        $request->method('getMethod')->willReturn($method);
        $request->method('getProtocolVersion')->willReturn($protocolVersion);
        $request->method('getHeaders')->willReturn($headers);

        return $request;
    }
}
