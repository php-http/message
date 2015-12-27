<?php

namespace spec\Http\Message;

use Http\Message\Cookie;
use PhpSpec\ObjectBehavior;

class CookieJarSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\CookieJar');
    }

    function it_is_an_iterator_aggregate()
    {
        $this->getIterator()->shouldHaveType('Iterator');
    }

    function it_has_a_cookie()
    {
        $cookie = new Cookie('name', 'value');

        $this->addCookie($cookie);

        $this->hasCookie($cookie)->shouldReturn(true);
        $this->hasCookies()->shouldReturn(true);
    }

    function it_accepts_a_cookie()
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name', 'value2');

        $this->addCookie($cookie);
        $this->addCookie($cookie2);

        $this->hasCookie($cookie)->shouldReturn(false);
        $this->hasCookie($cookie2)->shouldReturn(true);
    }

    function it_removes_a_cookie_with_an_empty_value()
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name');

        $this->addCookie($cookie);
        $this->addCookie($cookie2);

        $this->hasCookie($cookie)->shouldReturn(false);
        $this->hasCookie($cookie2)->shouldReturn(false);
    }

    function it_removes_a_cookie_with_a_lower_expiration_time()
    {
        $cookie = new Cookie('name', 'value', 100);
        $cookie2 = new Cookie('name', 'value', 1000);

        $this->addCookie($cookie);
        $this->addCookie($cookie2);

        $this->hasCookie($cookie)->shouldReturn(false);
        $this->hasCookie($cookie2)->shouldReturn(true);
    }

    function it_removes_a_cookie()
    {
        $cookie = new Cookie('name', 'value', 100);

        $this->addCookie($cookie);
        $this->removeCookie($cookie);

        $this->hasCookie($cookie)->shouldReturn(false);
    }

    function it_returns_all_cookies()
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name2', 'value');

        $this->addCookie($cookie);
        $this->addCookie($cookie2);

        $this->getCookies()->shouldBeAnArrayOfInstance('Http\Message\Cookie');
    }

    function it_returns_the_matching_cookies()
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name', 'value2');

        $this->addCookie($cookie);

        $this->getMatchingCookies($cookie2)->shouldBeAnArrayOfInstance('Http\Message\Cookie');
    }

    function it_sets_cookies()
    {
        $cookie = new Cookie('name', 'value');

        $this->setCookies([$cookie]);

        $this->hasCookie($cookie)->shouldReturn(true);
        $this->hasCookies()->shouldReturn(true);
        $this->count()->shouldReturn(1);
    }

    function it_accepts_cookies()
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name2', 'value');

        $this->addCookie($cookie);
        $this->addCookies([$cookie2]);

        $this->hasCookie($cookie)->shouldReturn(true);
        $this->hasCookie($cookie2)->shouldReturn(true);
        $this->hasCookies()->shouldReturn(true);
        $this->count()->shouldReturn(2);
    }

    function it_removes_cookies()
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name2', 'value');

        $this->addCookies([$cookie, $cookie2]);
        $this->removeCookies([$cookie2]);

        $this->hasCookie($cookie)->shouldReturn(true);
        $this->hasCookie($cookie2)->shouldReturn(false);
        $this->hasCookies()->shouldReturn(true);
        $this->count()->shouldReturn(1);
    }

    function it_removes_matching_cookies()
    {
        $cookie = new Cookie('name', 'value');
        $cookie2 = new Cookie('name2', 'value', 0, 'php-http.org');

        $this->addCookies([$cookie, $cookie2]);

        $this->removeMatchingCookies('name2', 'php-http.org', '/');

        $this->hasCookie($cookie)->shouldReturn(true);
        $this->hasCookie($cookie2)->shouldReturn(false);
        $this->hasCookies()->shouldReturn(true);
        $this->count()->shouldReturn(1);
    }

    function it_clears_cookies()
    {
        $cookie = new Cookie('name', 'value', 0, 'php-http.org');
        $cookie2 = new Cookie('name2', 'value');

        $this->addCookies([$cookie, $cookie2]);

        $this->clear();

        $this->hasCookies()->shouldReturn(false);
        $this->count()->shouldReturn(0);
    }

    public function getMatchers()
    {
        return [
            'beAnArrayOfInstance' => function ($subject, $instance) {
                foreach ($subject as $element) {
                    if (!$element instanceof $instance) {
                        return false;
                    }
                }

                return true;
            },
        ];
    }
}
