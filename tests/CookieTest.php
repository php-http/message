<?php

declare(strict_types=1);

namespace Http\Message\Tests;

use Http\Message\Cookie;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CookieTest extends TestCase
{
    public function testHasName(): void
    {
        self::assertSame('name', (new Cookie('name', 'value'))->getName());
    }

    public function testThrowsExceptionWhenNameIsEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Cookie('');
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function validCharacterProvider(): iterable
    {
        yield 'lowercase a' => ['a'];
        yield 'lowercase z' => ['z'];
        yield 'digit zero' => ['0'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidCharacterProvider(): iterable
    {
        yield 'null byte' => ["\x00"];
        yield 'space' => ["\x20"];
        yield 'delete' => ["\x7F"];
    }

    /**
     * @dataProvider validCharacterProvider
     */
    #[DataProvider('validCharacterProvider')]
    public function testAcceptsValidCharacters(string $value): void
    {
        $cookie = new Cookie($value, $value);

        self::assertSame($value, $cookie->getName());
        self::assertSame($value, $cookie->getValue());
        self::assertSame($value, $cookie->withValue($value)->getValue());
    }

    /**
     * @dataProvider invalidCharacterProvider
     */
    #[DataProvider('invalidCharacterProvider')]
    public function testThrowsExceptionWhenNameContainsInvalidCharacter(string $name): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Cookie($name);
    }

    /**
     * @dataProvider invalidCharacterProvider
     */
    #[DataProvider('invalidCharacterProvider')]
    public function testThrowsExceptionWhenValueContainsInvalidCharacter(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Cookie('name', $value);
    }

    /**
     * @dataProvider invalidCharacterProvider
     */
    #[DataProvider('invalidCharacterProvider')]
    public function testThrowsExceptionWhenNewValueContainsInvalidCharacter(string $value): void
    {
        $cookie = new Cookie('name', 'value');

        $this->expectException(\InvalidArgumentException::class);

        $cookie->withValue($value);
    }

    public function testHasValue(): void
    {
        $cookie = new Cookie('name', 'value');

        self::assertSame('value', $cookie->getValue());
        self::assertTrue($cookie->hasValue());
        self::assertFalse((new Cookie('name'))->hasValue());
    }

    public function testWithValueIsImmutable(): void
    {
        $cookie = new Cookie('name', 'value');
        $new = $cookie->withValue('value2');

        self::assertNotSame($cookie, $new);
        self::assertSame('value2', $new->getValue());
        self::assertSame('value', $cookie->getValue());
    }

    public function testHasMaxAge(): void
    {
        $cookie = new Cookie('name', 'value', 10);

        self::assertSame(10, $cookie->getMaxAge());
        self::assertTrue($cookie->hasMaxAge());
        self::assertFalse((new Cookie('name'))->hasMaxAge());
    }

    public function testWithMaxAge(): void
    {
        self::assertSame(1, (new Cookie('name', 'value'))->withMaxAge(1)->getMaxAge());
    }

    public function testThrowsExceptionWhenMaxAgeIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Cookie('name', 'value'))->withMaxAge('-1');
    }

    public function testHasExpires(): void
    {
        $expires = new \DateTime('+10 seconds');
        $cookie = new Cookie('name', 'value', null, null, null, false, false, $expires);

        self::assertSame($expires, $cookie->getExpires());
        self::assertTrue($cookie->hasExpires());
        self::assertFalse($cookie->isExpired());
    }

    public function testWithExpires(): void
    {
        $expires = new \DateTime('+10 seconds');

        self::assertSame($expires, (new Cookie('name', 'value'))->withExpires($expires)->getExpires());
    }

    public function testIsExpired(): void
    {
        $cookie = new Cookie('name', 'value', null, null, null, false, false, new \DateTime('-2 minutes'));

        self::assertTrue($cookie->hasExpires());
        self::assertTrue($cookie->isExpired());
    }

    public function testHasNoDomainByDefault(): void
    {
        $cookie = new Cookie('name', 'value');

        self::assertNull($cookie->getDomain());
        self::assertFalse($cookie->hasDomain());
    }

    public function testNormalizesDomain(): void
    {
        $cookie = new Cookie('name', 'value', null, '.PhP-hTtP.oRg');

        self::assertSame('php-http.org', $cookie->getDomain());
        self::assertTrue($cookie->hasDomain());
    }

    public function testWithDomain(): void
    {
        self::assertSame('php-http.org', (new Cookie('name', 'value'))->withDomain('.PhP-hTtP.oRg')->getDomain());
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function domainProvider(): iterable
    {
        yield 'same domain, different case' => ['PhP-hTtP.oRg', true];
        yield 'subdomain' => ['wWw.PhP-hTtP.oRg', true];
        yield 'ip address' => ['127.0.0.1', false];
        yield 'other domain' => ['httplug.io', false];
    }

    /**
     * @dataProvider domainProvider
     */
    #[DataProvider('domainProvider')]
    public function testMatchesDomain(string $domain, bool $expected): void
    {
        self::assertSame($expected, (new Cookie('name', 'value', null, 'php-http.org'))->matchDomain($domain));
    }

    public function testHasRootPathByDefault(): void
    {
        self::assertSame('/', (new Cookie('name', 'value'))->getPath());
    }

    public function testWithPath(): void
    {
        self::assertSame('/path', (new Cookie('name', 'value'))->withPath('/path')->getPath());
    }

    /**
     * @return iterable<string, array{string, string, bool}>
     */
    public static function pathProvider(): iterable
    {
        yield 'identical path' => ['/path/to/somewhere', '/path/to/somewhere', true];
        yield 'sub path' => ['/path/to/somewhere', '/path/to/somewhere/else', true];
        yield 'common prefix only' => ['/path/to/somewhere', '/path/to/somewhereelse', false];
        yield 'root matches root' => ['/', '/', true];
        yield 'root matches sub path' => ['/', '/cookies', true];
        yield 'root matches sub path with trailing slash' => ['/', '/cookies/', true];
    }

    /**
     * @dataProvider pathProvider
     */
    #[DataProvider('pathProvider')]
    public function testMatchesPath(string $cookiePath, string $path, bool $expected): void
    {
        self::assertSame($expected, (new Cookie('name', 'value', null, null, $cookiePath))->matchPath($path));
    }

    public function testIsNotSecureByDefault(): void
    {
        self::assertFalse((new Cookie('name', 'value'))->isSecure());
    }

    public function testWithSecure(): void
    {
        self::assertTrue((new Cookie('name', 'value'))->withSecure(true)->isSecure());
    }

    public function testIsNotHttpOnlyByDefault(): void
    {
        self::assertFalse((new Cookie('name', 'value'))->isHttpOnly());
    }

    public function testWithHttpOnly(): void
    {
        self::assertTrue((new Cookie('name', 'value'))->withHttpOnly(true)->isHttpOnly());
    }

    public function testMatchesOtherCookies(): void
    {
        $cookie = new Cookie('name', 'value', null, 'php-http.org');

        self::assertTrue($cookie->match(new Cookie('name', 'value2', null, 'php-http.org')));
        self::assertFalse($cookie->match(new Cookie('anotherName', 'value2', null, 'php-http.org')));
    }

    public function testIsValid(): void
    {
        self::assertTrue((new Cookie('name', 'value'))->isValid());
    }

    /**
     * @return iterable<string, array{list<mixed>}>
     */
    public static function invalidCookieArgumentsProvider(): iterable
    {
        yield 'invalid name' => [["\x20"]];
        yield 'invalid value' => [['name', "\x20"]];
        yield 'invalid max age' => [['name', 'value', '-1']];
    }

    /**
     * @param list<mixed> $arguments
     *
     * @dataProvider invalidCookieArgumentsProvider
     */
    #[DataProvider('invalidCookieArgumentsProvider')]
    public function testCanBeCreatedWithoutValidation(array $arguments): void
    {
        self::assertFalse(Cookie::createWithoutValidation(...$arguments)->isValid());
    }
}
