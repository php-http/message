<?php

declare(strict_types=1);

namespace Http\Message\Tests\Authentication;

use Http\Message\Authentication;
use Http\Message\Authentication\BasicAuth;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class BasicAuthTest extends TestCase
{
    public function testIsAnAuthentication(): void
    {
        self::assertInstanceOf(Authentication::class, new BasicAuth('john.doe', 'secret'));
    }

    public function testAuthenticatesRequest(): void
    {
        $newRequest = $this->createStub(RequestInterface::class);

        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())
            ->method('withHeader')
            ->with('Authorization', 'Basic '.base64_encode('john.doe:secret'))
            ->willReturn($newRequest);

        self::assertSame($newRequest, (new BasicAuth('john.doe', 'secret'))->authenticate($request));
    }
}
