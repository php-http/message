<?php

namespace spec\Http\Message\Authentication;

use Http\Message\Authentication;
use Psr\Http\Message\RequestInterface;
use PhpSpec\ObjectBehavior;

class ChainSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Authentication\Chain');
    }

    function it_is_an_authentication()
    {
        $this->shouldImplement('Http\Message\Authentication');
    }

    function it_throws_an_exception_when_non_authentication_is_passed()
    {
        $this->beConstructedWith(['authentication']);

        $this->shouldThrow('InvalidArgumentException')->duringInstantiation();
    }

    function it_authenticates_a_request(
        Authentication $auth1,
        Authentication $auth2,
        RequestInterface $originalRequest,
        RequestInterface $request1,
        RequestInterface $request2
    ) {
        $originalRequest->withHeader('AuthMethod1', 'AuthValue')->willReturn($request1);
        $request1->withHeader('AuthMethod2', 'AuthValue')->willReturn($request2);

        $auth1->authenticate($originalRequest)->will(function ($args) {
            return $args[0]->withHeader('AuthMethod1', 'AuthValue');
        });

        $auth2->authenticate($request1)->will(function ($args) {
            return $args[0]->withHeader('AuthMethod2', 'AuthValue');
        });

        $this->beConstructedWith([$auth1, $auth2]);

        $this->authenticate($originalRequest)->shouldReturn($request2);
    }
}
