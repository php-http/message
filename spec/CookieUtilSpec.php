<?php

namespace spec\Http\Message;

use Http\Message\Exception\UnexpectedValueException;
use PhpSpec\ObjectBehavior;

class CookieUtilSpec extends ObjectBehavior
{
    function it_parses_cookie_date_string_1()
    {
        $this->testCookieParse('Friday, 31 Jul 20 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000');
    }

    function it_parses_cookie_date_string_2()
    {
        $this->testCookieParse('Friday, 31-Jul-20 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000');
    }

    function it_parses_cookie_date_string_3()
    {
        $this->testCookieParse('Fri, 31-Jul-2020 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000');
    }

    function it_parses_cookie_date_string_4()
    {
        $this->testCookieParse('Fri, 31 Jul 2020 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000');
    }

    function it_parses_cookie_date_string_5()
    {
        $this->testCookieParse('Fri, 31-07-2020 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000');
    }

    function it_parses_cookie_date_string_6()
    {
        $this->testCookieParse('Fri, 31-07-20 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000');
    }

    function it_parses_cookie_date_string_7()
    {
        $this->testCookieParse('Friday, 31-Jul-20 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000');
    }

    function it_parses_cookie_date_string_8()
    {
        $this->testCookieParse('Fri Jul 31 08:49:37 2020', 'Friday, 31-Jul-2020 08:49:37 +0000');
    }

    function it_parses_cookie_date_string_9()
    {
        $this->testCookieParse('Friday July 31st 2020, 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000');
    }

    function it_parses_cookie_date_string_10()
    {
        // https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js#L52
        $this->testCookieParse('Wed, 09 Jun 2021 10:18:14 GMT', 'Wednesday, 09-Jun-2021 10:18:14 +0000');
    }

    function it_parses_cookie_date_string_11()
    {
        // https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js#L52
        $this->testCookieParse('Wed, 09 Jun 2021 22:18:14 GMT', 'Wednesday, 09-Jun-2021 22:18:14 +0000');
    }

    function it_parses_cookie_date_string_12()
    {
        // https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js#L52
        $this->testCookieParse('Tue, 18 Oct 2011 07:42:42.123 GMT', 'Tuesday, 18-Oct-2011 07:42:42 +0000');
    }

    function it_parses_cookie_date_string_13()
    {
        // https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js#L52
        $this->testCookieParse('18 Oct 2011 07:42:42 GMT', 'Tuesday, 18-Oct-2011 07:42:42 +0000');
    }

    function it_parses_cookie_date_string_14()
    {
        // https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js#L52
        $this->testCookieParse('8 Oct 2011 7:42:42 GMT', 'Saturday, 08-Oct-2011 07:42:42 +0000');
    }

    function it_parses_cookie_date_string_15()
    {
        // https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js#L52
        $this->testCookieParse('8 Oct 2011 7:2:42 GMT', 'Saturday, 08-Oct-2011 07:02:42 +0000');
    }

    function it_parses_cookie_date_string_16()
    {
        // https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js#L52
        $this->testCookieParse('Oct 18 2011 07:42:42 GMT', 'Tuesday, 18-Oct-2011 07:42:42 +0000');
    }

    function it_parses_cookie_date_string_17()
    {
        // https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js#L52
        $this->testCookieParse('Tue Oct 18 2011 07:05:03 GMT+0000 (GMT)', 'Tuesday, 18-Oct-2011 07:05:03 +0000');
    }

    function it_parses_cookie_date_string_18()
    {
        // https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js#L52
        $this->testCookieParse('09 Jun 2021 10:18:14 GMT', 'Wednesday, 09-Jun-2021 10:18:14 +0000');
    }

    function it_parses_cookie_date_string_19()
    {
        // https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js#L52
        $this->testCookieParse('01 Jan 1970 00:00:00 GMT', 'Thursday, 01-Jan-1970 00:00:00 +0000');
    }

    function it_parses_cookie_date_string_20()
    {
        // https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js#L52
        $this->testCookieParse('01 Jan 1601 00:00:00 GMT', 'Monday, 01-Jan-1601 00:00:00 +0000');
    }

    function it_parses_cookie_date_string_21()
    {
        // implicit year
        $this->testCookieParse('10 Feb 81 13:00:00 GMT', 'Tuesday, 10-Feb-1981 13:00:00 +0000');
    }

    function it_parses_cookie_date_string_22()
    {
        // dashes
        $this->testCookieParse('Thu, 17-Apr-2014 02:12:29 GMT', 'Thursday, 17-Apr-2014 02:12:29 +0000');
    }

    function it_parses_cookie_date_string_23()
    {
        // dashes and UTC
        $this->testCookieParse('Thu, 17-Apr-2014 02:12:29 UTC', 'Thursday, 17-Apr-2014 02:12:29 +0000');
    }

    function it_throws_an_exception_if_cookie_date_string_is_unparseable_1()
    {
        $this->beConstructedThrough('parseDate', ['Flursday July 31st 2020, 08:49:37 GMT']);
        $this->shouldThrow('Http\Message\Exception\UnexpectedValueException');
    }

    function it_throws_an_exception_if_cookie_date_string_is_unparseable_2()
    {
        $this->beConstructedThrough('parseDate', ['99 Jix 3038 48:86:72 ZMT']);
        $this->shouldThrow('Http\Message\Exception\UnexpectedValueException');
    }

    private function testCookieParse(string $input, string $output)
    {
        $this->beConstructedThrough('parseDate', [$input]);
        $this->shouldHaveType('\DateTime');
        $this->format('l, d-M-Y H:i:s O')->shouldReturn($output);
    }
}
