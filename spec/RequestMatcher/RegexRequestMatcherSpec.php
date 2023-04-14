<?php

namespace spec\Http\Message\RequestMatcher;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class RegexRequestMatcherSpec extends ObjectBehavior
{
    public function let($regex)
    {
        $this->beConstructedWith($regex);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\RequestMatcher\RegexRequestMatcher');
    }

    public function it_is_a_request_matcher()
    {
        $this->shouldImplement('Http\Message\RequestMatcher');
    }

    public function it_matches_a_request(RequestInterface $request, UriInterface $uri)
    {
        $this->beConstructedWith('/test/');

        $request->getUri()->willReturn($uri);
        $uri->__toString()->willReturn('/test');

        $this->matches($request)->shouldReturn(true);
    }

    public function it_does_not_match_a_request(RequestInterface $request, UriInterface $uri)
    {
        $this->beConstructedWith('/test/');

        $request->getUri()->willReturn($uri);
        $uri->__toString()->willReturn('/ttttt');

        $this->matches($request)->shouldReturn(false);
    }
}
