<?php

namespace spec\Http\Message\RequestMatcher;

use Http\Message\RequestMatcher;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class RegexRequestMatcherSpec extends ObjectBehavior
{
    function let($regex)
    {
        $this->beConstructedWith($regex);
    }

    function it_is_a_request_matcher()
    {
        $this->shouldImplement('Http\Message\RequestMatcher');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\RequestMatcher\RegexRequestMatcher');
    }

    function it_matches(RequestInterface $request, UriInterface $uri)
    {
        $this->beConstructedWith('/test/');

        $request->getUri()->willReturn($uri);
        $uri->__toString()->willReturn('/test');

        $this->matches($request)->shouldReturn(true);
    }

    function it_does_not_match(RequestInterface $request, UriInterface $uri)
    {
        $this->beConstructedWith('/test/');

        $request->getUri()->willReturn($uri);
        $uri->__toString()->willReturn('/ttttt');

        $this->matches($request)->shouldReturn(false);
    }
}
