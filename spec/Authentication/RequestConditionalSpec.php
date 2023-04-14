<?php

namespace spec\Http\Message\Authentication;

use Http\Message\Authentication;
use Http\Message\RequestMatcher;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;

class RequestConditionalSpec extends ObjectBehavior
{
    public function let(RequestMatcher $requestMatcher, Authentication $authentication)
    {
        $this->beConstructedWith($requestMatcher, $authentication);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Authentication\RequestConditional');
    }

    public function it_is_an_authentication()
    {
        $this->shouldImplement('Http\Message\Authentication');
    }

    public function it_authenticates_a_request(
        Authentication $authentication,
        RequestMatcher $requestMatcher,
        RequestInterface $request,
        RequestInterface $newRequest
    ) {
        $requestMatcher->matches($request)->willReturn(true);
        $authentication->authenticate($request)->willReturn($newRequest);

        $this->authenticate($request)->shouldReturn($newRequest);
    }

    public function it_does_not_authenticate_a_request(
        Authentication $authentication,
        RequestMatcher $requestMatcher,
        RequestInterface $request
    ) {
        $requestMatcher->matches($request)->willReturn(false);
        $authentication->authenticate($request)->shouldNotBeCalled();

        $this->authenticate($request)->shouldReturn($request);
    }
}
