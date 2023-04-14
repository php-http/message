<?php

namespace spec\Http\Message\Authentication;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;

class BearerSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('token');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Authentication\Bearer');
    }

    public function it_is_an_authentication()
    {
        $this->shouldImplement('Http\Message\Authentication');
    }

    public function it_authenticates_a_request(RequestInterface $request, RequestInterface $newRequest)
    {
        $request->withHeader('Authorization', 'Bearer token')->willReturn($newRequest);

        $this->authenticate($request)->shouldReturn($newRequest);
    }
}
