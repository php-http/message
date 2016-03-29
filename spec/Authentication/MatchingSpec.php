<?php

namespace spec\Http\Message\Authentication;

use Http\Message\Authentication;
use Psr\Http\Message\RequestInterface;
use PhpSpec\ObjectBehavior;

class MatchingSpec extends ObjectBehavior
{
    function let(Authentication $authentication)
    {
        $matcher = function($request) { return true; };

        $this->beConstructedWith($authentication, $matcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Authentication\Matching');
    }

    function it_is_an_authentication()
    {
        $this->shouldImplement('Http\Message\Authentication');
    }

    function it_authenticates_a_request(Authentication $authentication, RequestInterface $request, RequestInterface $newRequest)
    {
        $authentication->authenticate($request)->willReturn($newRequest);

        $this->authenticate($request)->shouldReturn($newRequest);
    }

    function it_does_not_authenticate_a_request(Authentication $authentication, RequestInterface $request)
    {
        $matcher = function($request) { return false; };

        $this->beConstructedWith($authentication, $matcher);

        $authentication->authenticate($request)->shouldNotBeCalled();

        $this->authenticate($request)->shouldReturn($request);
    }

    function it_creates_a_matcher_from_url(Authentication $authentication)
    {
        $this->createUrlMatcher($authentication, 'url')->shouldHaveType('Http\Message\Authentication\Matching');
    }
}
