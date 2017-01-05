<?php

namespace spec\Http\Message;

use Http\Message\Cookie;
use PhpSpec\ObjectBehavior;

class CookieSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('name', 'value');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Cookie');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('name');
    }

    /**
     * @dataProvider invalidCharacterExamples
     */
    function it_throws_an_exception_when_the_name_contains_invalid_character($name, $shouldThrow)
    {
        $this->beConstructedWith($name);

        if ($shouldThrow) {
            $expectation = $this->shouldThrow('InvalidArgumentException');
        } else {
            $expectation = $this->shouldNotThrow('InvalidArgumentException');
        }

        $expectation->duringInstantiation();
    }

    function it_throws_an_expection_when_name_is_empty()
    {
        $this->beConstructedWith('');

        $this->shouldThrow('InvalidArgumentException')->duringInstantiation();
    }

    function it_has_a_value()
    {
        $this->getValue()->shouldReturn('value');
        $this->hasValue()->shouldReturn(true);
    }

    /**
     * @dataProvider invalidCharacterExamples
     */
    function it_throws_an_exception_when_the_value_contains_invalid_character($value, $shouldThrow)
    {
        $this->beConstructedWith('name', $value);

        if ($shouldThrow) {
            $expectation = $this->shouldThrow('InvalidArgumentException');
        } else {
            $expectation = $this->shouldNotThrow('InvalidArgumentException');
        }

        $expectation->duringInstantiation();
    }

    function it_accepts_a_value()
    {
        $cookie = $this->withValue('value2');

        $cookie->shouldHaveType('Http\Message\Cookie');
        $cookie->getValue()->shouldReturn('value2');
    }

    /**
     * @dataProvider invalidCharacterExamples
     */
    function it_throws_an_exception_when_the_new_value_contains_invalid_character($value, $shouldThrow)
    {
        if ($shouldThrow) {
            $expectation = $this->shouldThrow('InvalidArgumentException');
        } else {
            $expectation = $this->shouldNotThrow('InvalidArgumentException');
        }

        $expectation->duringWithValue($value);
    }

    function it_has_a_max_age_time()
    {
        $this->beConstructedWith('name', 'value', 10);

        $this->getMaxAge()->shouldReturn(10);
        $this->hasMaxAge()->shouldReturn(true);
    }

    function it_accepts_a_max_age()
    {
        $cookie = $this->withMaxAge(1);

        $cookie->shouldHaveType('Http\Message\Cookie');
        $cookie->getMaxAge()->shouldReturn(1);
    }

    function it_throws_an_exception_when_max_age_is_invalid()
    {
        $this->shouldThrow('InvalidArgumentException')->duringWithMaxAge('-1');
    }

    function it_has_an_expires_attribute()
    {
        $expires = new \DateTime('+10 seconds');

        $this->beConstructedWith('name', 'value', null, null, null, false, false, $expires);

        $this->getExpires()->shouldReturn($expires);
        $this->hasExpires()->shouldReturn(true);
        $this->isExpired()->shouldReturn(false);
    }

    function it_accepts_an_expires_attribute()
    {
        $expires = new \DateTime('+10 seconds');

        $cookie = $this->withExpires($expires);

        $cookie->shouldHaveType('Http\Message\Cookie');
        $cookie->getExpires()->shouldReturn($expires);
    }

    function it_is_expired()
    {
        $this->beConstructedWith('name', 'value', null, null, null, false, false, new \DateTime('-2 minutes'));

        $this->getExpires()->shouldHaveType('DateTime');
        $this->hasExpires()->shouldReturn(true);
        $this->isExpired()->shouldReturn(true);
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
        $this->matchPath('/path/to/somewhere/else')->shouldReturn(true);
        $this->matchPath('/path/to/somewhereelse')->shouldReturn(false);
    }

    function it_matches_the_root_path()
    {
        $this->beConstructedWith('name', 'value', null, null, '/');

        $this->matchPath('/')->shouldReturn(true);
        $this->matchPath('/cookies')->shouldReturn(true);
        $this->matchPath('/cookies/')->shouldReturn(true);
    }

    function it_is_secure()
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

    function it_matches_other_cookies()
    {
        $this->beConstructedWith('name', 'value', null, 'php-http.org');

        $matches = new Cookie('name', 'value2', null, 'php-http.org');
        $notMatches = new Cookie('anotherName', 'value2', null, 'php-http.org');

        $this->match($matches)->shouldReturn(true);
        $this->match($notMatches)->shouldReturn(false);
    }

    function it_validates_itself()
    {
        $this->isValid()->shouldReturn(true);
    }

    function it_can_be_constructed_without_name_validation()
    {
        $this->beConstructedThrough('createWithoutValidation', ["\x20"]);

        $this->isValid()->shouldReturn(false);
    }

    function it_can_be_constructed_without_value_validation()
    {
        $this->beConstructedThrough('createWithoutValidation', ['name', "\x20"]);

        $this->isValid()->shouldReturn(false);
    }

    function it_can_be_constructed_without_max_age_validation()
    {
        $this->beConstructedThrough('createWithoutValidation', ['name', 'value', '-1']);

        $this->isValid()->shouldReturn(false);
    }

    /**
     * Provides examples for invalid characers in names and values.
     *
     * @return array
     */
    public function invalidCharacterExamples()
    {
        return [
            ['a', false],
            ["\x00", true],
            ['z', false],
            ["\x20", true],
            ['0', false],
            ["\x7F", true]
        ];
    }
}
