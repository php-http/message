<?php

declare(strict_types=1);

namespace Http\Message\Tests\Builder;

use GuzzleHttp\Psr7\Response;
use Http\Message\Builder\ResponseBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class ResponseBuilderTest extends TestCase
{
    public function testReturnsGivenResponse(): void
    {
        $response = $this->createStub(ResponseInterface::class);

        self::assertSame($response, (new ResponseBuilder($response))->getResponse());
    }

    public function testReadsHeadersFromArray(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('withStatus')->with(200, 'OK')->willReturnSelf();
        $response->expects($this->once())->method('withProtocolVersion')->with('1.1')->willReturnSelf();
        $response->expects($this->once())->method('hasHeader')->with('Content-type')->willReturn(false);
        $response->expects($this->once())->method('withHeader')->with('Content-type', 'text/html')->willReturnSelf();

        $builder = new ResponseBuilder($response);

        self::assertSame($builder, $builder->setHeadersFromArray(['HTTP/1.1 200 OK', 'Content-type: text/html']));
    }

    /**
     * @see https://github.com/php-http/message/issues/41
     */
    public function testSplitsHeadersCorrectly(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('withStatus')->with(200, 'OK')->willReturnSelf();
        $response->expects($this->once())->method('withProtocolVersion')->with('1.1')->willReturnSelf();
        $response->expects($this->once())->method('hasHeader')->with('Content-type')->willReturn(false);
        $response->expects($this->once())->method('withHeader')->with('Content-type', 'application/xml+atom')->willReturnSelf();

        (new ResponseBuilder($response))->setHeadersFromString("HTTP/1.1 200 OK\r\nContent-type: application/xml+atom\r\n");
    }

    public function testAddsRepeatedHeaders(): void
    {
        $builder = new ResponseBuilder(new Response());
        $builder->setHeadersFromString("HTTP/2 404 Not Found\r\nSet-Cookie: a=1\r\nSet-Cookie: b=2\r\n");

        $response = $builder->getResponse();
        self::assertSame(404, $response->getStatusCode());
        self::assertSame('Not Found', $response->getReasonPhrase());
        self::assertSame('2', $response->getProtocolVersion());
        self::assertSame(['a=1', 'b=2'], $response->getHeader('Set-Cookie'));
    }

    public function testThrowsExceptionForInvalidStatusLine(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ResponseBuilder(new Response()))->setStatus('FOO 200');
    }

    public function testThrowsExceptionForInvalidHeaderLine(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ResponseBuilder(new Response()))->addHeader('no colon here');
    }

    public function testThrowsExceptionForNonStringHeaders(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ResponseBuilder(new Response()))->setHeadersFromString(['HTTP/1.1 200 OK']);
    }
}
