<?php

declare(strict_types=1);

namespace Http\Message\Tests\Authentication;

use Http\Message\Authentication;
use Http\Message\Authentication\AutoBasicAuth;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class AutoBasicAuthTest extends TestCase
{
    public function testIsAnAuthentication(): void
    {
        self::assertInstanceOf(Authentication::class, new AutoBasicAuth());
    }

    public function testAuthenticatesRequestAndRemovesUserInfo(): void
    {
        $authenticatedRequest = $this->createStub(RequestInterface::class);
        $uriWithoutUserInfo = $this->createStub(UriInterface::class);

        $requestWithoutUserInfo = $this->createMock(RequestInterface::class);
        $requestWithoutUserInfo->expects($this->once())
            ->method('withHeader')
            ->with('Authorization', 'Basic '.base64_encode('username:password'))
            ->willReturn($authenticatedRequest);

        $uri = $this->createMock(UriInterface::class);
        $uri->method('getUserInfo')->willReturn('username:password');
        $uri->expects($this->once())->method('withUserInfo')->with('')->willReturn($uriWithoutUserInfo);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getUri')->willReturn($uri);
        $request->expects($this->once())->method('withUri')->with($uriWithoutUserInfo)->willReturn($requestWithoutUserInfo);

        self::assertSame($authenticatedRequest, (new AutoBasicAuth())->authenticate($request));
    }

    public function testAuthenticatesRequestWithoutPassword(): void
    {
        $authenticatedRequest = $this->createStub(RequestInterface::class);
        $uriWithoutUserInfo = $this->createStub(UriInterface::class);

        $requestWithoutUserInfo = $this->createMock(RequestInterface::class);
        $requestWithoutUserInfo->expects($this->once())
            ->method('withHeader')
            ->with('Authorization', 'Basic '.base64_encode('username'))
            ->willReturn($authenticatedRequest);

        $uri = $this->createMock(UriInterface::class);
        $uri->method('getUserInfo')->willReturn('username');
        $uri->expects($this->once())->method('withUserInfo')->with('')->willReturn($uriWithoutUserInfo);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getUri')->willReturn($uri);
        $request->expects($this->once())->method('withUri')->with($uriWithoutUserInfo)->willReturn($requestWithoutUserInfo);

        self::assertSame($authenticatedRequest, (new AutoBasicAuth())->authenticate($request));
    }

    public function testDoesNotAuthenticateRequestWithoutUserInfo(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('getUserInfo')->willReturn('');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getUri')->willReturn($uri);
        $request->expects($this->never())->method('withHeader');
        $request->expects($this->never())->method('withUri');

        self::assertSame($request, (new AutoBasicAuth())->authenticate($request));
    }

    public function testAuthenticatesRequestWithoutRemovingUserInfo(): void
    {
        $authenticatedRequest = $this->createStub(RequestInterface::class);

        $uri = $this->createMock(UriInterface::class);
        $uri->method('getUserInfo')->willReturn('username:password');
        $uri->expects($this->never())->method('withUserInfo');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getUri')->willReturn($uri);
        $request->expects($this->never())->method('withUri');
        $request->expects($this->once())
            ->method('withHeader')
            ->with('Authorization', 'Basic '.base64_encode('username:password'))
            ->willReturn($authenticatedRequest);

        self::assertSame($authenticatedRequest, (new AutoBasicAuth(false))->authenticate($request));
    }
}
