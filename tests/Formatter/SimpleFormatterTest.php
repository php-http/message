<?php

declare(strict_types=1);

namespace Http\Message\Tests\Formatter;

use Http\Message\Formatter;
use Http\Message\Formatter\SimpleFormatter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

final class SimpleFormatterTest extends TestCase
{
    public function testIsAFormatter(): void
    {
        self::assertInstanceOf(Formatter::class, new SimpleFormatter());
    }

    public function testFormatsRequest(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('__toString')->willReturn('http://foo.com/bar');

        $request = $this->createStub(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn($uri);
        $request->method('getProtocolVersion')->willReturn('1.1');

        self::assertSame('GET http://foo.com/bar 1.1', (new SimpleFormatter())->formatRequest($request));
    }

    public function testFormatsResponse(): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getReasonPhrase')->willReturn('OK');
        $response->method('getProtocolVersion')->willReturn('1.1');
        $response->method('getStatusCode')->willReturn(200);

        $formatter = new SimpleFormatter();

        self::assertSame('200 OK 1.1', $formatter->formatResponse($response));
        self::assertSame('200 OK 1.1', $formatter->formatResponseForRequest($response, $this->createStub(RequestInterface::class)));
    }
}
