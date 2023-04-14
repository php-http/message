<?php

namespace spec\Http\Message\Authentication;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;

class WsseSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('john.doe', 'secret');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Authentication\Wsse');
    }

    public function it_is_an_authentication()
    {
        $this->shouldImplement('Http\Message\Authentication');
    }

    public function it_authenticates_a_request(
        RequestInterface $request,
        RequestInterface $newRequest,
        RequestInterface $newerRequest
    ) {
        $request->withHeader('Authorization', 'WSSE profile="UsernameToken"')->willReturn($newRequest);
        $newRequest->withHeader('X-WSSE', Argument::that(function ($arg) {
            return preg_match('/UsernameToken Username=".*", PasswordDigest=".*", Nonce=".*", Created=".*"/', $arg);
        }))->willReturn($newerRequest);

        $this->authenticate($request)->shouldReturn($newerRequest);
    }
}
