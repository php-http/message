<?php

declare(strict_types=1);

namespace Http\Message\Tests\RequestMatcher;

use Http\Message\RequestMatcher;
use Http\Message\RequestMatcher\CallbackRequestMatcher;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class CallbackRequestMatcherTest extends TestCase
{
    public function testIsARequestMatcher(): void
    {
        self::assertInstanceOf(RequestMatcher::class, new CallbackRequestMatcher(static function (): bool {
            return true;
        }));
    }

    public function testPassesRequestToCallback(): void
    {
        $request = $this->createStub(RequestInterface::class);
        $received = null;

        $matcher = new CallbackRequestMatcher(static function (RequestInterface $r) use (&$received): bool {
            $received = $r;

            return true;
        });

        self::assertTrue($matcher->matches($request));
        self::assertSame($request, $received);
    }

    public function testDoesNotMatchRequest(): void
    {
        $matcher = new CallbackRequestMatcher(static function (): bool {
            return false;
        });

        self::assertFalse($matcher->matches($this->createStub(RequestInterface::class)));
    }
}
