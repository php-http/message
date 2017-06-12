<?php

namespace spec\Http\Message;

use Http\Message\Exception\UnexpectedValueException;
use PhpSpec\ObjectBehavior;

class CookieUtilSpec extends ObjectBehavior
{
    /**
     *  @dataProvider getCookieStrings
     */
    function it_parses_cookie_date_string($cookieDateString, $expectedString)
    {
        $this->beConstructedThrough('parseDate', [$cookieDateString]);
        $this->shouldHaveType('\DateTime');
        $this->format('l, d-M-Y H:i:s O')->shouldReturn($expectedString);
    }

    /**
     *  @dataProvider getInvalidCookieDateStrings
     */
    function it_throws_an_exception_if_cookie_date_string_is_unparseable($cookieDateString)
    {
        $this->beConstructedThrough('parseDate', [$cookieDateString]);
        $this->shouldThrow('Http\Message\Exception\UnexpectedValueException');
    }

    /**
     * Provides examples for valid cookie date string.
     *
     * @return array
     */
    public function getCookieStrings()
    {
        return [
            ['Friday, 31 Jul 20 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'],
            ['Friday, 31-Jul-20 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'],
            ['Fri, 31-Jul-2020 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'],
            ['Fri, 31 Jul 2020 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'],
            ['Fri, 31-07-2020 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'],
            ['Fri, 31-07-20 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'],
            ['Friday, 31-Jul-20 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'],
            ['Fri Jul 31 08:49:37 2020', 'Friday, 31-Jul-2020 08:49:37 +0000'],
            ['Friday July 31st 2020, 08:49:37 GMT', 'Friday, 31-Jul-2020 08:49:37 +0000'],
            // https://github.com/salesforce/tough-cookie/blob/master/test/date_test.js#L52
            ['Wed, 09 Jun 2021 10:18:14 GMT', 'Wednesday, 09-Jun-2021 10:18:14 +0000'],
            ['Wed, 09 Jun 2021 22:18:14 GMT', 'Wednesday, 09-Jun-2021 22:18:14 +0000'],
            ['Tue, 18 Oct 2011 07:42:42.123 GMT', 'Tuesday, 18-Oct-2011 07:42:42 +0000'],
            ['18 Oct 2011 07:42:42 GMT', 'Tuesday, 18-Oct-2011 07:42:42 +0000'],
            ['8 Oct 2011 7:42:42 GMT', 'Saturday, 08-Oct-2011 07:42:42 +0000'],
            ['8 Oct 2011 7:2:42 GMT', 'Saturday, 08-Oct-2011 07:02:42 +0000'],
            ['Oct 18 2011 07:42:42 GMT', 'Tuesday, 18-Oct-2011 07:42:42 +0000'],
            ['Tue Oct 18 2011 07:05:03 GMT+0000 (GMT)', 'Tuesday, 18-Oct-2011 07:05:03 +0000'],
            ['09 Jun 2021 10:18:14 GMT', 'Wednesday, 09-Jun-2021 10:18:14 +0000'],
            ['01 Jan 1970 00:00:00 GMT', 'Thursday, 01-Jan-1970 00:00:00 +0000'],
            ['01 Jan 1601 00:00:00 GMT', 'Monday, 01-Jan-1601 00:00:00 +0000'],
            ['10 Feb 81 13:00:00 GMT', 'Tuesday, 10-Feb-1981 13:00:00 +0000'], // implicit year
            ['Thu, 17-Apr-2014 02:12:29 GMT', 'Thursday, 17-Apr-2014 02:12:29 +0000'], // dashes
            ['Thu, 17-Apr-2014 02:12:29 UTC', 'Thursday, 17-Apr-2014 02:12:29 +0000'], // dashes and UTC
        ];
    }

    /**
     * Provides examples for invalid cookie date string.
     *
     * @return array
     */
    public function getInvalidCookieDateStrings()
    {
        return [
            ['Flursday July 31st 2020, 08:49:37 GMT'],
            ['99 Jix 3038 48:86:72 ZMT'],
        ];
    }
}
