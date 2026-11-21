<?php

declare(strict_types=1);

namespace Http\Message\Tests\Authentication;

use GuzzleHttp\Psr7\Request;
use Http\Message\Authentication;
use Http\Message\Authentication\Wsse;
use PHPUnit\Framework\TestCase;

final class WsseTest extends TestCase
{
    private const HEADER_PATTERN = '/^UsernameToken Username="(?<username>[^"]*)", PasswordDigest="(?<digest>[^"]*)", Nonce="(?<nonce>[^"]*)", Created="(?<created>[^"]*)"$/';

    public function testIsAnAuthentication(): void
    {
        self::assertInstanceOf(Authentication::class, new Wsse('john.doe', 'secret'));
    }

    public function testThrowsExceptionForUnknownHashAlgorithm(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Wsse('john.doe', 'secret', 'not-an-algorithm');
    }

    public function testAuthenticatesRequest(): void
    {
        $request = (new Wsse('john.doe', 'secret'))->authenticate(new Request('GET', '/'));

        self::assertSame('WSSE profile="UsernameToken"', $request->getHeaderLine('Authorization'));
        $this->assertValidWsseHeader($request->getHeaderLine('X-WSSE'), 'john.doe', 'secret', 'sha1');
    }

    public function testAuthenticatesRequestWithCustomHashAlgorithm(): void
    {
        $request = (new Wsse('john.doe', 'secret', 'sha512'))->authenticate(new Request('GET', '/'));

        $this->assertValidWsseHeader($request->getHeaderLine('X-WSSE'), 'john.doe', 'secret', 'sha512');
    }

    private function assertValidWsseHeader(string $header, string $username, string $password, string $algorithm): void
    {
        self::assertSame(1, preg_match(self::HEADER_PATTERN, $header, $matches), sprintf('Malformed X-WSSE header "%s".', $header));
        self::assertSame($username, $matches['username']);

        $expectedDigest = base64_encode(hash($algorithm, base64_decode($matches['nonce']).$matches['created'].$password, true));
        self::assertSame($expectedDigest, $matches['digest']);
    }
}
