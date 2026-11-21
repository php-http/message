<?php

declare(strict_types=1);

namespace Http\Message\Tests\Authentication;

use Http\Message\Authentication;
use Http\Message\Authentication\Bearer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class BearerTest extends TestCase
{
    public function testIsAnAuthentication(): void
    {
        self::assertInstanceOf(Authentication::class, new Bearer('token'));
    }

    public function testAuthenticatesRequest(): void
    {
        $newRequest = $this->createStub(RequestInterface::class);

        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())
            ->method('withHeader')
            ->with('Authorization', 'Bearer token')
            ->willReturn($newRequest);

        self::assertSame($newRequest, (new Bearer('token'))->authenticate($request));
    }
}
