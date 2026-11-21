<?php

declare(strict_types=1);

namespace Http\Message\Tests\RequestMatcher;

use GuzzleHttp\Psr7\Request;
use Http\Message\RequestMatcher as RequestMatcherInterface;
use Http\Message\RequestMatcher\RequestMatcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RequestMatcherTest extends TestCase
{
    public function testIsARequestMatcher(): void
    {
        self::assertInstanceOf(RequestMatcherInterface::class, new RequestMatcher());
    }

    public function testMatchesEverythingWithoutConstraints(): void
    {
        self::assertTrue((new RequestMatcher())->matches(new Request('DELETE', 'ftp://example.com/anything')));
    }

    /**
     * @return iterable<string, array{RequestMatcher, string, string, bool}>
     */
    public static function matchProvider(): iterable
    {
        yield 'path matches' => [new RequestMatcher('^/tes?'), 'GET', 'http://php-http.org/test/foo', true];
        yield 'path does not match' => [new RequestMatcher('^/tes?'), 'GET', 'http://php-http.org/ttttt', false];
        yield 'path is url decoded' => [new RequestMatcher('^/hello world$'), 'GET', 'http://php-http.org/hello%20world', true];
        yield 'host matches' => [new RequestMatcher(null, 'php-htt?'), 'GET', 'http://php-http.org/', true];
        yield 'host matches case insensitive' => [new RequestMatcher(null, 'PHP-HTTP'), 'GET', 'http://php-http.org/', true];
        yield 'host does not match' => [new RequestMatcher(null, 'php-htt?'), 'GET', 'http://httplug.io/', false];
        yield 'method matches' => [new RequestMatcher(null, null, 'get'), 'GET', 'http://php-http.org/', true];
        yield 'one of many methods matches' => [new RequestMatcher(null, null, ['get', 'post']), 'POST', 'http://php-http.org/', true];
        yield 'method does not match' => [new RequestMatcher(null, null, 'get'), 'POST', 'http://php-http.org/', false];
        yield 'scheme matches' => [new RequestMatcher(null, null, [], 'http'), 'GET', 'http://php-http.org/', true];
        yield 'scheme does not match' => [new RequestMatcher(null, null, [], 'http'), 'GET', 'https://php-http.org/', false];
        yield 'all constraints match' => [new RequestMatcher('^/api', 'example\.com$', 'POST', 'https'), 'POST', 'https://example.com/api/users', true];
        yield 'one constraint fails' => [new RequestMatcher('^/api', 'example\.com$', 'POST', 'https'), 'GET', 'https://example.com/api/users', false];
    }

    /**
     * @dataProvider matchProvider
     */
    #[DataProvider('matchProvider')]
    public function testMatches(RequestMatcher $matcher, string $method, string $uri, bool $expected): void
    {
        self::assertSame($expected, $matcher->matches(new Request($method, $uri)));
    }
}
