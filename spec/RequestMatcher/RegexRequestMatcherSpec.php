<?php

namespace spec\Http\Message\RequestMatcher;

use Http\Message\RequestMatcher;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use PhpSpec\ObjectBehavior;

class RegexRequestMatcherSpec extends ObjectBehavior
{
    function let($regex)
    {
        $this->beConstructedWith($regex);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\RequestMatcher\RegexRequestMatcher');
    }

    function it_is_a_request_matcher()
    {
        $this->shouldImplement('Http\Message\RequestMatcher');
    }

    function it_matches_a_request(RequestInterface $request, UriInterface $uri)
    {
        $this->beConstructedWith('/test/');

        $request->getUri()->willReturn($uri);
        $uri->__toString()->willReturn('/test');

        $this->matches($request)->shouldReturn(true);
    }

    function it_does_not_match_a_request(RequestInterface $request, UriInterface $uri)
    {
        $this->beConstructedWith('/test/');

        $request->getUri()->willReturn($uri);
        $uri->__toString()->willReturn('/ttttt');

        $this->matches($request)->shouldReturn(false);
    }
}
