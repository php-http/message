<?php

declare(strict_types=1);

namespace Http\Message\Tests\UriFactory;

use Http\Message\UriFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

abstract class UriFactoryTestCase extends TestCase
{
    abstract protected function createFactory(): UriFactory;

    public function testCreatesUriFromString(): void
    {
        $uri = $this->createFactory()->createUri('http://php-http.org/path?query#fragment');

        self::assertSame('http', $uri->getScheme());
        self::assertSame('php-http.org', $uri->getHost());
        self::assertSame('/path', $uri->getPath());
        self::assertSame('query', $uri->getQuery());
        self::assertSame('fragment', $uri->getFragment());
    }

    public function testReturnsGivenUri(): void
    {
        $uri = $this->createStub(UriInterface::class);

        self::assertSame($uri, $this->createFactory()->createUri($uri));
    }

    public function testThrowsExceptionWhenUriIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->createFactory()->createUri(null);
    }
}
