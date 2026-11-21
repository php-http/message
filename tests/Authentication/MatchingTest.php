<?php

declare(strict_types=1);

namespace Http\Message\Tests\Authentication;

use Http\Message\Authentication;
use Http\Message\Authentication\Matching;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class MatchingTest extends TestCase
{
    public function testIsAnAuthentication(): void
    {
        self::assertInstanceOf(Authentication::class, new Matching($this->createStub(Authentication::class)));
    }

    public function testAuthenticatesMatchingRequest(): void
    {
        $request = $this->createStub(RequestInterface::class);
        $newRequest = $this->createStub(RequestInterface::class);

        $authentication = $this->createMock(Authentication::class);
        $authentication->expects($this->once())->method('authenticate')->with($request)->willReturn($newRequest);

        $matching = new Matching($authentication, function () {
            return true;
        });

        self::assertSame($newRequest, $matching->authenticate($request));
    }

    public function testDoesNotAuthenticateNonMatchingRequest(): void
    {
        $request = $this->createStub(RequestInterface::class);

        $authentication = $this->createMock(Authentication::class);
        $authentication->expects($this->never())->method('authenticate');

        $matching = new Matching($authentication, function () {
            return false;
        });

        self::assertSame($request, $matching->authenticate($request));
    }

    public function testCreatesUrlMatcher(): void
    {
        $matchingRequest = $this->createStub(RequestInterface::class);
        $matchingRequest->method('getRequestTarget')->willReturn('/api/users');
        $otherRequest = $this->createStub(RequestInterface::class);
        $otherRequest->method('getRequestTarget')->willReturn('/public');
        $authenticatedRequest = $this->createStub(RequestInterface::class);

        $authentication = $this->createMock(Authentication::class);
        $authentication->expects($this->once())->method('authenticate')->with($matchingRequest)->willReturn($authenticatedRequest);

        $matching = Matching::createUrlMatcher($authentication, '#^/api#');

        self::assertSame($authenticatedRequest, $matching->authenticate($matchingRequest));
        self::assertSame($otherRequest, $matching->authenticate($otherRequest));
    }
}
