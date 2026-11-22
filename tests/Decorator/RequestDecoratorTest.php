<?php

declare(strict_types=1);

namespace Http\Message\Tests\Decorator;

use Http\Message\Decorator\RequestDecorator;
use Http\Message\Tests\Fixtures\RequestDecoratorStub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class RequestDecoratorTest extends TestCase
{
    public function testUsesRequestDecoratorTrait(): void
    {
        self::assertContains(RequestDecorator::class, class_uses(RequestDecoratorStub::class));
    }

    public function testHasRequest(): void
    {
        $request = $this->createStub(RequestInterface::class);

        self::assertSame($request, (new RequestDecoratorStub($request))->getRequest());
    }

    public function testWithRequestReturnsNewDecorator(): void
    {
        $request = $this->createStub(RequestInterface::class);
        $otherRequest = $this->createStub(RequestInterface::class);
        $decorator = new RequestDecoratorStub($request);

        $new = $decorator->withRequest($otherRequest);

        self::assertInstanceOf(RequestDecoratorStub::class, $new);
        self::assertSame($otherRequest, $new->getRequest());
        self::assertSame($request, $decorator->getRequest());
    }

    public function testDelegatesGetRequestTarget(): void
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getRequestTarget')->willReturn('/');

        self::assertSame('/', (new RequestDecoratorStub($request))->getRequestTarget());
    }

    public function testDelegatesGetMethod(): void
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');

        self::assertSame('GET', (new RequestDecoratorStub($request))->getMethod());
    }

    public function testDelegatesGetUri(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $request = $this->createStub(RequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        self::assertSame($uri, (new RequestDecoratorStub($request))->getUri());
    }

    public function testWithRequestTargetReturnsNewDecorator(): void
    {
        $newRequest = $this->createStub(RequestInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())->method('withRequestTarget')->with('/')->willReturn($newRequest);

        $new = (new RequestDecoratorStub($request))->withRequestTarget('/');

        self::assertInstanceOf(RequestDecoratorStub::class, $new);
        self::assertSame($newRequest, $new->getMessage());
    }

    public function testWithMethodReturnsNewDecorator(): void
    {
        $newRequest = $this->createStub(RequestInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())->method('withMethod')->with('GET')->willReturn($newRequest);

        $new = (new RequestDecoratorStub($request))->withMethod('GET');

        self::assertInstanceOf(RequestDecoratorStub::class, $new);
        self::assertSame($newRequest, $new->getMessage());
    }

    public function testWithUriReturnsNewDecorator(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $newRequest = $this->createStub(RequestInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())->method('withUri')->with($uri, false)->willReturn($newRequest);

        $new = (new RequestDecoratorStub($request))->withUri($uri);

        self::assertInstanceOf(RequestDecoratorStub::class, $new);
        self::assertSame($newRequest, $new->getMessage());
    }

    public function testWithUriPassesPreserveHost(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $newRequest = $this->createStub(RequestInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())->method('withUri')->with($uri, true)->willReturn($newRequest);

        self::assertSame($newRequest, (new RequestDecoratorStub($request))->withUri($uri, true)->getMessage());
    }
}
