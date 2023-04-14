<?php

namespace spec\Http\Message\RequestMatcher;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;

class CallbackRequestMatcherSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(function () {});
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\RequestMatcher\CallbackRequestMatcher');
    }

    public function it_is_a_request_matcher()
    {
        $this->shouldImplement('Http\Message\RequestMatcher');
    }

    public function it_matches_a_request(RequestInterface $request)
    {
        $callback = function () { return true; };

        $this->beConstructedWith($callback);

        $this->matches($request)->shouldReturn(true);
    }

    public function it_does_not_match_a_request(RequestInterface $request)
    {
        $callback = function () { return false; };

        $this->beConstructedWith($callback);

        $this->matches($request)->shouldReturn(false);
    }
}
