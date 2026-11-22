<?php

declare(strict_types=1);

namespace Http\Message\Tests\Authentication;

use Http\Message\Authentication;
use Http\Message\Authentication\QueryParam;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class QueryParamTest extends TestCase
{
    public function testIsAnAuthentication(): void
    {
        self::assertInstanceOf(Authentication::class, new QueryParam([]));
    }

    public function testAuthenticatesRequestByMergingQueryParameters(): void
    {
        $newUri = $this->createStub(UriInterface::class);
        $newRequest = $this->createStub(RequestInterface::class);

        $uri = $this->createMock(UriInterface::class);
        $uri->method('getQuery')->willReturn('param1=value1&param2[]=value2');
        $uri->expects($this->once())
            ->method('withQuery')
            ->with('param1=value1&param2%5B0%5D=value2&username=username&password=password')
            ->willReturn($newUri);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getUri')->willReturn($uri);
        $request->expects($this->once())->method('withUri')->with($newUri)->willReturn($newRequest);

        $authentication = new QueryParam([
            'username' => 'username',
            'password' => 'password',
        ]);

        self::assertSame($newRequest, $authentication->authenticate($request));
    }
}
