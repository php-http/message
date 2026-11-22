<?php

declare(strict_types=1);

namespace Http\Message\Tests\Authentication;

use Http\Message\Authentication;
use Http\Message\Authentication\Header;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class HeaderTest extends TestCase
{
    public function testIsAnAuthentication(): void
    {
        self::assertInstanceOf(Authentication::class, new Header('X-AUTH-TOKEN', 'REAL'));
    }

    public function testAuthenticatesRequest(): void
    {
        $newRequest = $this->createStub(RequestInterface::class);

        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())
            ->method('withHeader')
            ->with('X-AUTH-TOKEN', 'REAL')
            ->willReturn($newRequest);

        self::assertSame($newRequest, (new Header('X-AUTH-TOKEN', 'REAL'))->authenticate($request));
    }
}
