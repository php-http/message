<?php

namespace spec\Http\Message;

use Http\Message\Cookie;
use PhpSpec\ObjectBehavior;

class CookieSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('name', 'value', 0, null, null, false, false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Cookie');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('name');
    }

    function it_throws_an_exception_when_the_name_contains_invalid_character()
    {
        for ($i = 0; $i < 128; $i++) {
            $name = chr($i);

            if (preg_match('/[\x00-\x20\x22\x28-\x29\x2c\x2f\x3a-\x40\x5b-\x5d\x7b\x7d\x7f]/', $name)) {
                $expectation = $this->shouldThrow('InvalidArgumentException');
            } else {
                $expectation = $this->shouldNotThrow('InvalidArgumentException');
            }

            $expectation->during('__construct', [$name]);
        }
    }

    function it_throws_an_expection_when_name_is_empty()
    {
        $this->shouldThrow('InvalidArgumentException')->during('__construct', ['']);
    }

    function it_has_a_value()
    {
        $this->getValue()->shouldReturn('value');
        $this->hasValue()->shouldReturn(true);
    }

    function it_throws_an_exception_when_the_value_contains_invalid_character()
    {
        for ($i = 0; $i < 128; $i++) {
            $value = chr($i);

            if (preg_match('/[^\x21\x23-\x2B\x2D-\x3A\x3C-\x5B\x5D-\x7E]/', $value)) {
                $expectation = $this->shouldThrow('InvalidArgumentException');
            } else {
                $expectation = $this->shouldNotThrow('InvalidArgumentException');
            }

            $expectation->during('__construct', ['name', $value]);
        }
    }

    function it_accepts_a_value()
    {
        $cookie = $this->withValue('value2');

        $cookie->shouldHaveType('Http\Message\Cookie');
        $cookie->getValue()->shouldReturn('value2');
    }

    function it_throws_an_exception_when_the_new_value_contains_invalid_character()
    {
        for ($i = 0; $i < 128; $i++) {
            $value = chr($i);

            if (preg_match('/[^\x21\x23-\x2B\x2D-\x3A\x3C-\x5B\x5D-\x7E]/', $value)) {
                $expectation = $this->shouldThrow('InvalidArgumentException');
            } else {
                $expectation = $this->shouldNotThrow('InvalidArgumentException');
            }

            $expectation->duringWithValue($value);
        }
    }

    function it_has_an_invalid_max_age_time()
    {
        $this->getMaxAge()->shouldReturn(0);
        $this->getExpires()->shouldHaveType('DateTime');
        $this->hasMaxAge()->shouldReturn(true);
        $this->hasExpires()->shouldReturn(true);
        $this->isExpired()->shouldReturn(true);
    }

    function it_has_a_max_age_time()
    {
        $this->beConstructedWith('name', 'value', 10);

        $this->getMaxAge()->shouldReturn(10);
        $this->getExpires()->shouldHaveType('DateTime');
        $this->hasMaxAge()->shouldReturn(true);
        $this->hasExpires()->shouldReturn(true);
        $this->isExpired()->shouldReturn(false);
    }

    function it_accepts_a_max_age()
    {
        $cookie = $this->withMaxAge(1);

        $cookie->shouldHaveType('Http\Message\Cookie');
        $cookie->getMaxAge()->shouldReturn(1);
        $cookie->getExpires()->shouldReturn($this->getExpires());
    }

    function it_accepts_an_expiration_time()
    {
        $cookie = $this->withExpiration(1);

        $cookie->shouldHaveType('Http\Message\Cookie');
        $cookie->getMaxAge()->shouldReturn(1);
        $cookie->getExpires()->shouldHaveType('DateTime');
    }

    function it_has_an_expires_attribute()
    {
        $expires = new \DateTime('+10 seconds');

        $this->beConstructedWith('name', 'value', $expires);

        $this->getMaxAge()->shouldReturn(null);
        $this->getExpires()->shouldReturn($expires);
        $this->hasMaxAge()->shouldReturn(false);
        $this->hasExpires()->shouldReturn(true);
        $this->isExpired()->shouldReturn(false);
    }

    function it_accepts_an_expires_attribute()
    {
        $expires = new \DateTime('+10 seconds');

        $cookie = $this->withExpires($expires);

        $cookie->shouldHaveType('Http\Message\Cookie');
        $cookie->getMaxAge()->shouldReturn(0);
        $cookie->getExpires()->shouldReturn($expires);
    }

    function it_is_expired()
    {
        $this->beConstructedWith('name', 'value', -10);

        $this->getMaxAge()->shouldReturn(-10);
        $this->getExpires()->shouldHaveType('DateTime');
        $this->hasMaxAge()->shouldReturn(true);
        $this->hasExpires()->shouldReturn(true);
        $this->isExpired()->shouldReturn(true);
    }

    function it_has_an_infinite_expiration_time()
    {
        $this->beConstructedWith('name', 'value', null);

        $this->getMaxAge()->shouldReturn(null);
        $this->getExpires()->shouldReturn(null);
        $this->hasMaxAge()->shouldReturn(false);
        $this->hasExpires()->shouldReturn(false);
        $this->isExpired()->shouldReturn(false);
    }

    function it_has_a_domain()
    {
        $this->getDomain()->shouldReturn(null);
        $this->hasDomain()->shouldReturn(false);
    }

    function it_has_a_valid_domain()
    {
        $this->beConstructedWith('name', 'value', null, '.PhP-hTtP.oRg');

        $this->getDomain()->shouldReturn('php-http.org');
        $this->hasDomain()->shouldReturn(true);
    }

    function it_accepts_a_domain()
    {
        $cookie = $this->withDomain('.PhP-hTtP.oRg');

        $cookie->shouldHaveType('Http\Message\Cookie');
        $cookie->getDomain()->shouldReturn('php-http.org');
    }

    function it_matches_a_domain()
    {
        $this->beConstructedWith('name', 'value', null, 'php-http.org');

        $this->matchDomain('PhP-hTtP.oRg')->shouldReturn(true);
        $this->matchDomain('127.0.0.1')->shouldReturn(false);
        $this->matchDomain('wWw.PhP-hTtP.oRg')->shouldReturn(true);
    }

    function it_has_a_path()
    {
        $this->getPath()->shouldReturn('/');
    }

    function it_accepts_a_path()
    {
        $cookie = $this->withPath('/path');

        $cookie->shouldHaveType('Http\Message\Cookie');
        $cookie->getPath()->shouldReturn('/path');
    }

    function it_matches_a_path()
    {
        $this->beConstructedWith('name', 'value', null, null, '/path/to/somewhere');

        $this->matchPath('/path/to/somewhere')->shouldReturn(true);
        $this->matchPath('/path/to/somewhereelse')->shouldReturn(false);
    }

    function it_can_be_secure()
    {
        $this->isSecure()->shouldReturn(false);
    }

    function it_accepts_security()
    {
        $cookie = $this->withSecure(true);

        $cookie->shouldHaveType('Http\Message\Cookie');
        $cookie->isSecure()->shouldReturn(true);
    }

    function it_can_be_http_only()
    {
        $this->isHttpOnly()->shouldReturn(false);
    }

    function it_accepts_http_only()
    {
        $cookie = $this->withHttpOnly(true);

        $cookie->shouldHaveType('Http\Message\Cookie');
        $cookie->isHttpOnly()->shouldReturn(true);
    }

    function it_matches_another_cookies()
    {
        $this->beConstructedWith('name', 'value', null, 'php-http.org');

        $matches = new Cookie('name', 'value2', null, 'php-http.org');
        $notMatches = new Cookie('anotherName', 'value2', null, 'php-http.org');

        $this->match($matches)->shouldReturn(true);
        $this->match($notMatches)->shouldReturn(false);
    }
}
