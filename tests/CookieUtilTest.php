<?php

declare(strict_types=1);

namespace Http\Message\Tests;

use Http\Message\CookieUtil;
use Http\Message\Exception\UnexpectedValueException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CookieUtilTest extends TestCase
{
    /**
     * @see https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js
     *
     * @return iterable<string, array{string, string}>
     */
    public static function dateProvider(): iterable
    {
        yield 'RFC 850, two digit year' => ['Friday, 31 Jul 20 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'];
        yield 'RFC 850 with dashes' => ['Friday, 31-Jul-20 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'];
        yield 'dashes, four digit year' => ['Fri, 31-Jul-2020 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'];
        yield 'RFC 1123' => ['Fri, 31 Jul 2020 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'];
        yield 'numeric month, four digit year' => ['Fri, 31-07-2020 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'];
        yield 'numeric month, two digit year' => ['Fri, 31-07-20 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'];
        yield 'asctime' => ['Fri Jul 31 08:49:37 2020', 'Friday, 31-Jul-2020 08:49:37 +0000'];
        yield 'free form' => ['Friday July 31st 2020, 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'];
        yield 'morning' => ['Wed, 09 Jun 2021 10:18:14 GMT', 'Wednesday, 09-Jun-2021 10:18:14 +0000'];
        yield 'evening' => ['Wed, 09 Jun 2021 22:18:14 GMT', 'Wednesday, 09-Jun-2021 22:18:14 +0000'];
        yield 'milliseconds' => ['Tue, 18 Oct 2011 07:42:42.123 GMT', 'Tuesday, 18-Oct-2011 07:42:42 +0000'];
        yield 'without weekday' => ['18 Oct 2011 07:42:42 GMT', 'Tuesday, 18-Oct-2011 07:42:42 +0000'];
        yield 'single digit day and hour' => ['8 Oct 2011 7:42:42 GMT', 'Saturday, 08-Oct-2011 07:42:42 +0000'];
        yield 'single digit minute' => ['8 Oct 2011 7:2:42 GMT', 'Saturday, 08-Oct-2011 07:02:42 +0000'];
        yield 'month first' => ['Oct 18 2011 07:42:42 GMT', 'Tuesday, 18-Oct-2011 07:42:42 +0000'];
        yield 'javascript toString' => ['Tue Oct 18 2011 07:05:03 GMT+0000 (GMT)', 'Tuesday, 18-Oct-2011 07:05:03 +0000'];
        yield 'leading zero day without weekday' => ['09 Jun 2021 10:18:14 GMT', 'Wednesday, 09-Jun-2021 10:18:14 +0000'];
        yield 'unix epoch' => ['01 Jan 1970 00:00:00 GMT', 'Thursday, 01-Jan-1970 00:00:00 +0000'];
        yield 'windows epoch' => ['01 Jan 1601 00:00:00 GMT', 'Monday, 01-Jan-1601 00:00:00 +0000'];
        yield 'implicit century' => ['10 Feb 81 13:00:00 GMT', 'Tuesday, 10-Feb-1981 13:00:00 +0000'];
        yield 'dashes with GMT' => ['Thu, 17-Apr-2014 02:12:29 GMT', 'Thursday, 17-Apr-2014 02:12:29 +0000'];
        yield 'dashes with UTC' => ['Thu, 17-Apr-2014 02:12:29 UTC', 'Thursday, 17-Apr-2014 02:12:29 +0000'];
    }

    /**
     * @dataProvider dateProvider
     */
    #[DataProvider('dateProvider')]
    public function testParsesCookieDate(string $input, string $expected): void
    {
        self::assertSame($expected, CookieUtil::parseDate($input)->format('l, d-M-Y H:i:s O'));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function unparseableDateProvider(): iterable
    {
        yield 'unknown weekday' => ['Flursday July 31st 2020, 08:49:37 GMT'];
        yield 'garbage' => ['99 Jix 3038 48:86:72 ZMT'];
    }

    /**
     * @dataProvider unparseableDateProvider
     */
    #[DataProvider('unparseableDateProvider')]
    public function testThrowsExceptionForUnparseableDate(string $input): void
    {
        $this->expectException(UnexpectedValueException::class);

        CookieUtil::parseDate($input);
    }

    public function testThrowsExceptionForNonStringDate(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CookieUtil::parseDate(123);
    }
}
