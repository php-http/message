<?php

declare(strict_types=1);

namespace Http\Message\Tests\MessageFactory;

use Http\Message\MessageFactory;
use PHPUnit\Framework\TestCase;

abstract class MessageFactoryTestCase extends TestCase
{
    abstract protected function createFactory(): MessageFactory;

    public function testCreatesRequest(): void
    {
        $request = $this->createFactory()->createRequest('GET', '/', ['X-hello' => 'world'], 'lorem');

        self::assertSame('GET', $request->getMethod());
        self::assertSame('/', $request->getRequestTarget());
        self::assertSame('lorem', (string) $request->getBody());
        self::assertSame('world', $request->getHeaderLine('X-hello'));
        self::assertSame('1.1', $request->getProtocolVersion());
    }

    public function testCreatesRequestWithProtocolVersion(): void
    {
        $request = $this->createFactory()->createRequest('POST', 'http://php-http.org/', [], null, '1.0');

        self::assertSame('POST', $request->getMethod());
        self::assertSame('1.0', $request->getProtocolVersion());
    }

    public function testCreatesResponse(): void
    {
        $response = $this->createFactory()->createResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    public function testCreatesResponseWithArguments(): void
    {
        $response = $this->createFactory()->createResponse(404, 'Not Found', ['X-hello' => 'world'], 'lorem', '1.0');

        self::assertSame(404, $response->getStatusCode());
        self::assertSame('Not Found', $response->getReasonPhrase());
        self::assertSame('world', $response->getHeaderLine('X-hello'));
        self::assertSame('lorem', (string) $response->getBody());
        self::assertSame('1.0', $response->getProtocolVersion());
    }
}
