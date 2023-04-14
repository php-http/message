<?php

namespace spec\Http\Message\Authentication;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;

class HeaderSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('X-AUTH-TOKEN', 'REAL');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Authentication\Header');
    }

    public function it_is_an_authentication()
    {
        $this->shouldImplement('Http\Message\Authentication');
    }

    public function it_authenticates_a_request(RequestInterface $request, RequestInterface $newRequest)
    {
        $request->withHeader('X-AUTH-TOKEN', 'REAL')->willReturn($newRequest);

        $this->authenticate($request)->shouldReturn($newRequest);
    }
}
