<?php

namespace spec\Http\Message\RequestMatcher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;

class CallbackRequestMatcherSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(function () {});
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\RequestMatcher\CallbackRequestMatcher');
    }

    function it_is_a_request_matcher()
    {
        $this->shouldImplement('Http\Message\RequestMatcher');
    }

    function it_matches_a_request(RequestInterface $request)
    {
        $callback = function () { return true; };

        $this->beConstructedWith($callback);

        $this->matches($request)->shouldReturn(true);
    }

    function it_does_not_match_a_request(RequestInterface $request)
    {
        $callback = function () { return false; };

        $this->beConstructedWith($callback);

        $this->matches($request)->shouldReturn(false);
    }
}
