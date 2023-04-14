<?php

namespace spec\Http\Message\Authentication;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;

class BasicAuthSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('john.doe', 'secret');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Authentication\BasicAuth');
    }

    public function it_is_an_authentication()
    {
        $this->shouldImplement('Http\Message\Authentication');
    }

    public function it_authenticates_a_request(RequestInterface $request, RequestInterface $newRequest)
    {
        $request->withHeader('Authorization', 'Basic '.base64_encode('john.doe:secret'))->willReturn($newRequest);

        $this->authenticate($request)->shouldReturn($newRequest);
    }
}
