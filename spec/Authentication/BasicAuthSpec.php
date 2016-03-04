<?php

namespace spec\Http\Message\Authentication;

use Psr\Http\Message\RequestInterface;
use PhpSpec\ObjectBehavior;

class BasicAuthSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('john.doe', 'secret');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Authentication\BasicAuth');
    }

    function it_is_an_authentication()
    {
        $this->shouldImplement('Http\Message\Authentication');
    }

    function it_authenticates_a_request(RequestInterface $request, RequestInterface $newRequest)
    {
        $request->withHeader('Authorization', 'Basic '.base64_encode('john.doe:secret'))->willReturn($newRequest);

        $this->authenticate($request)->shouldReturn($newRequest);
    }
}
