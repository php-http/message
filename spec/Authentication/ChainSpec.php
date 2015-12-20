<?php

namespace spec\Http\Message\Authentication;

use Http\Message\Authentication;
use Psr\Http\Message\RequestInterface;
use PhpSpec\ObjectBehavior;

class ChainSpec extends ObjectBehavior
{
    use AuthenticationBehavior;

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Authentication\Chain');
    }

    function it_accepts_an_authentication_chain_in_the_constructor(Authentication $auth1, Authentication $auth2)
    {
        $chain = [$auth1, $auth2];

        $this->beConstructedWith($chain);

        $this->getAuthenticationChain()->shouldReturn($chain);
    }

    function it_sets_the_authentication_chain(Authentication $auth1, Authentication $auth2)
    {
        // This SHOULD be replaced
        $this->beConstructedWith([$auth1]);

        $this->setAuthenticationChain([$auth2]);

        $this->getAuthenticationChain()->shouldReturn([$auth2]);
    }

    function it_adds_an_authentication_method(Authentication $auth1, Authentication $auth2)
    {
        // This SHOULD NOT be replaced
        $this->beConstructedWith([$auth1]);

        $this->addAuthentication($auth2);

        $this->getAuthenticationChain()->shouldReturn([$auth1, $auth2]);
    }

    function it_clears_the_authentication_chain(Authentication $auth1, Authentication $auth2)
    {
        // This SHOULD be replaced
        $this->beConstructedWith([$auth1]);

        $this->clearAuthenticationChain();

        $this->addAuthentication($auth2);

        $this->getAuthenticationChain()->shouldReturn([$auth2]);
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
