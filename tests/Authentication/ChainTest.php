<?php

declare(strict_types=1);

namespace Http\Message\Tests\Authentication;

use Http\Message\Authentication;
use Http\Message\Authentication\Chain;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class ChainTest extends TestCase
{
    public function testIsAnAuthentication(): void
    {
        self::assertInstanceOf(Authentication::class, new Chain());
    }

    public function testThrowsExceptionWhenNonAuthenticationIsPassed(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Chain(['authentication']);
    }

    public function testReturnsRequestUnchangedWhenChainIsEmpty(): void
    {
        $request = $this->createStub(RequestInterface::class);

        self::assertSame($request, (new Chain())->authenticate($request));
    }

    public function testAuthenticatesRequestThroughAllMembersInOrder(): void
    {
        $originalRequest = $this->createStub(RequestInterface::class);
        $request1 = $this->createStub(RequestInterface::class);
        $request2 = $this->createStub(RequestInterface::class);

        $auth1 = $this->createMock(Authentication::class);
        $auth1->expects($this->once())->method('authenticate')->with($originalRequest)->willReturn($request1);

        $auth2 = $this->createMock(Authentication::class);
        $auth2->expects($this->once())->method('authenticate')->with($request1)->willReturn($request2);

        self::assertSame($request2, (new Chain([$auth1, $auth2]))->authenticate($originalRequest));
    }
}
