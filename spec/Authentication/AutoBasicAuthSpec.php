<?php

namespace spec\Http\Message\Authentication;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class AutoBasicAuthSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Authentication\AutoBasicAuth');
    }

    function it_is_an_authentication()
    {
        $this->shouldImplement('Http\Message\Authentication');
    }

    function it_authenticates_a_request(
        RequestInterface $request,
        UriInterface $uri,
        UriInterface $uriWithoutUserInfo,
        RequestInterface $requestWithoutUserInfo,
        RequestInterface $authenticatedRequest
    ) {
        $request->getUri()->willReturn($uri);
        $uri->getUserInfo()->willReturn('username:password');
        $uri->withUserInfo('', null)->willReturn($uriWithoutUserInfo);
        $request->withUri($uriWithoutUserInfo)->willReturn($requestWithoutUserInfo);
        $requestWithoutUserInfo
            ->withHeader('Authorization', 'Basic '.base64_encode('username:password'))
            ->willReturn($authenticatedRequest)
        ;

        $this->authenticate($request)->shouldReturn($authenticatedRequest);
    }

    function it_authenticates_a_request_without_password(
        RequestInterface $request,
        UriInterface $uri,
        UriInterface $uriWithoutUserInfo,
        RequestInterface $requestWithoutUserInfo,
        RequestInterface $authenticatedRequest
    ) {
        $request->getUri()->willReturn($uri);
        $uri->getUserInfo()->willReturn('username');
        $uri->withUserInfo('', null)->willReturn($uriWithoutUserInfo);
        $request->withUri($uriWithoutUserInfo)->willReturn($requestWithoutUserInfo);
        $requestWithoutUserInfo
            ->withHeader('Authorization', 'Basic '.base64_encode('username'))
            ->willReturn($authenticatedRequest)
        ;

        $this->authenticate($request)->shouldReturn($authenticatedRequest);
    }

    function it_does_not_authenticate_a_request(RequestInterface $request, UriInterface $uri)
    {
        $request->getUri()->willReturn($uri);
        $uri->getUserInfo()->willReturn('');

        $this->authenticate($request)->shouldReturn($request);
    }

    function it_authenticates_a_request_without_user_info_removal(
        RequestInterface $request,
        UriInterface $uri,
        RequestInterface $authenticatedRequest
    ) {
        $this->beConstructedWith(false);

        $request->getUri()->willReturn($uri);
        $uri->getUserInfo()->willReturn('username:password');
        $request
            ->withHeader('Authorization', 'Basic '.base64_encode('username:password'))
            ->willReturn($authenticatedRequest)
        ;

        $this->authenticate($request)->shouldReturn($authenticatedRequest);
    }
}
