<?php

declare(strict_types=1);

namespace Http\Message\Tests\Authentication;

use Http\Message\Authentication;
use Http\Message\Authentication\RequestConditional;
use Http\Message\RequestMatcher;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class RequestConditionalTest extends TestCase
{
    public function testIsAnAuthentication(): void
    {
        $authentication = new RequestConditional(
            $this->createStub(RequestMatcher::class),
            $this->createStub(Authentication::class)
        );

        self::assertInstanceOf(Authentication::class, $authentication);
    }

    public function testAuthenticatesMatchingRequest(): void
    {
        $request = $this->createStub(RequestInterface::class);
        $newRequest = $this->createStub(RequestInterface::class);

        $requestMatcher = $this->createMock(RequestMatcher::class);
        $requestMatcher->expects($this->once())->method('matches')->with($request)->willReturn(true);

        $authentication = $this->createMock(Authentication::class);
        $authentication->expects($this->once())->method('authenticate')->with($request)->willReturn($newRequest);

        self::assertSame($newRequest, (new RequestConditional($requestMatcher, $authentication))->authenticate($request));
    }

    public function testDoesNotAuthenticateNonMatchingRequest(): void
    {
        $request = $this->createStub(RequestInterface::class);

        $requestMatcher = $this->createMock(RequestMatcher::class);
        $requestMatcher->expects($this->once())->method('matches')->with($request)->willReturn(false);

        $authentication = $this->createMock(Authentication::class);
        $authentication->expects($this->never())->method('authenticate');

        self::assertSame($request, (new RequestConditional($requestMatcher, $authentication))->authenticate($request));
    }
}
