<?php

declare(strict_types=1);

namespace Http\Message\Tests\RequestMatcher;

use Http\Message\RequestMatcher;
use Http\Message\RequestMatcher\RegexRequestMatcher;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class RegexRequestMatcherTest extends TestCase
{
    public function testIsARequestMatcher(): void
    {
        self::assertInstanceOf(RequestMatcher::class, new RegexRequestMatcher('/test/'));
    }

    public function testMatchesRequest(): void
    {
        self::assertTrue((new RegexRequestMatcher('/test/'))->matches($this->createRequest('/test')));
    }

    public function testDoesNotMatchRequest(): void
    {
        self::assertFalse((new RegexRequestMatcher('/test/'))->matches($this->createRequest('/ttttt')));
    }

    private function createRequest(string $uri): RequestInterface
    {
        $uriStub = $this->createStub(UriInterface::class);
        $uriStub->method('__toString')->willReturn($uri);

        $request = $this->createStub(RequestInterface::class);
        $request->method('getUri')->willReturn($uriStub);

        return $request;
    }
}
