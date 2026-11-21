<?php

declare(strict_types=1);

namespace Http\Message\Tests;

use Http\Message\Cookie;
use Http\Message\CookieJar;
use PHPUnit\Framework\TestCase;

final class CookieJarTest extends TestCase
{
    public function testIsCountableAndIterable(): void
    {
        $jar = new CookieJar();

        self::assertInstanceOf(\Countable::class, $jar);
        self::assertInstanceOf(\Iterator::class, $jar->getIterator());
        self::assertCount(0, $jar);
    }

    public function testIteratesOverCookies(): void
    {
        $cookie = new Cookie('name', 'value');
        $jar = new CookieJar();
        $jar->addCookie($cookie);

        self::assertSame([$cookie], iterator_to_array($jar->getIterator(), false));
    }

    public function testHasCookie(): void
    {
        $cookie = new Cookie('name', 'value');
        $jar = new CookieJar();

        $jar->addCookie($cookie);

        self::assertTrue($jar->hasCookie($cookie));
        self::assertTrue($jar->hasCookies());
    }

    public function testReplacesCookieWithDifferentValue(): void
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name', 'value2');
        $jar = new CookieJar();

        $jar->addCookie($cookie);
        $jar->addCookie($cookie2);

        self::assertFalse($jar->hasCookie($cookie));
        self::assertTrue($jar->hasCookie($cookie2));
    }

    public function testRemovesCookieWhenNewCookieHasEmptyValue(): void
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name');
        $jar = new CookieJar();

        $jar->addCookie($cookie);
        $jar->addCookie($cookie2);

        self::assertFalse($jar->hasCookie($cookie));
        self::assertFalse($jar->hasCookie($cookie2));
    }

    public function testReplacesCookieWithLowerMaxAge(): void
    {
        $cookie = new Cookie('name', 'value', 100);
        $cookie2 = new Cookie('name', 'value', 1000);
        $jar = new CookieJar();

        $jar->addCookie($cookie);
        $jar->addCookie($cookie2);

        self::assertFalse($jar->hasCookie($cookie));
        self::assertTrue($jar->hasCookie($cookie2));
    }

    public function testRemovesCookie(): void
    {
        $cookie = new Cookie('name', 'value', 100);
        $jar = new CookieJar();

        $jar->addCookie($cookie);
        $jar->removeCookie($cookie);

        self::assertFalse($jar->hasCookie($cookie));
    }

    public function testReturnsAllCookies(): void
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name2', 'value');
        $jar = new CookieJar();

        $jar->addCookie($cookie);
        $jar->addCookie($cookie2);

        self::assertSame([$cookie, $cookie2], $jar->getCookies());
    }

    public function testReturnsMatchingCookies(): void
    {
        $cookie = new Cookie('name', 'value');
        $other = new Cookie('other', 'value');
        $jar = new CookieJar();

        $jar->addCookies([$cookie, $other]);

        self::assertSame([$cookie], $jar->getMatchingCookies(new Cookie('name', 'value2')));
    }

    public function testSetsCookies(): void
    {
        $cookie = new Cookie('name', 'value');
        $jar = new CookieJar();
        $jar->addCookie(new Cookie('previous', 'value'));

        $jar->setCookies([$cookie]);

        self::assertTrue($jar->hasCookie($cookie));
        self::assertCount(1, $jar);
    }

    public function testAddsCookies(): void
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name2', 'value');
        $jar = new CookieJar();

        $jar->addCookie($cookie);
        $jar->addCookies([$cookie2]);

        self::assertTrue($jar->hasCookie($cookie));
        self::assertTrue($jar->hasCookie($cookie2));
        self::assertCount(2, $jar);
    }

    public function testRemovesCookies(): void
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name2', 'value');
        $jar = new CookieJar();

        $jar->addCookies([$cookie, $cookie2]);
        $jar->removeCookies([$cookie2]);

        self::assertTrue($jar->hasCookie($cookie));
        self::assertFalse($jar->hasCookie($cookie2));
        self::assertCount(1, $jar);
    }

    public function testRemovesMatchingCookies(): void
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name2', 'value', 0, 'php-http.org');
        $jar = new CookieJar();

        $jar->addCookies([$cookie, $cookie2]);
        $jar->removeMatchingCookies('name2', 'php-http.org', '/');

        self::assertTrue($jar->hasCookie($cookie));
        self::assertFalse($jar->hasCookie($cookie2));
        self::assertCount(1, $jar);
    }

    public function testClearsCookies(): void
    {
        $jar = new CookieJar();
        $jar->addCookies([new Cookie('name', 'value', 0, 'php-http.org'), new Cookie('name2', 'value')]);

        $jar->clear();

        self::assertFalse($jar->hasCookies());
        self::assertCount(0, $jar);
    }
}
