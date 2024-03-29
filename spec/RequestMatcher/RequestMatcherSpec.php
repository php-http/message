<?php

namespace spec\Http\Message\RequestMatcher;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class RequestMatcherSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\RequestMatcher\RequestMatcher');
    }

    public function it_is_a_request_matcher()
    {
        $this->shouldImplement('Http\Message\RequestMatcher');
    }

    public function it_matches_a_path(RequestInterface $request, UriInterface $uri)
    {
        $this->beConstructedWith('^/tes?');

        $request->getUri()->willReturn($uri);
        $uri->getPath()->willReturn('/test/foo');

        $this->matches($request)->shouldReturn(true);
    }

    public function it_does_not_match_a_path(RequestInterface $request, UriInterface $uri)
    {
        $this->beConstructedWith('#^/tes?#');

        $request->getUri()->willReturn($uri);
        $uri->getPath()->willReturn('/ttttt');

        $this->matches($request)->shouldReturn(false);
    }

    public function it_matches_a_host(RequestInterface $request, UriInterface $uri)
    {
        $this->beConstructedWith(null, 'php-htt?');

        $request->getUri()->willReturn($uri);
        $uri->getHost()->willReturn('php-http.org');

        $this->matches($request)->shouldReturn(true);
    }

    public function it_does_not_match_a_host(RequestInterface $request, UriInterface $uri)
    {
        $this->beConstructedWith(null, 'php-htt?');

        $request->getUri()->willReturn($uri);
        $uri->getHost()->willReturn('httplug.io');

        $this->matches($request)->shouldReturn(false);
    }

    public function it_matches_a_method(RequestInterface $request)
    {
        $this->beConstructedWith(null, null, 'get');

        $request->getMethod()->willReturn('GET');

        $this->matches($request)->shouldReturn(true);
    }

    public function it_does_not_match_a_method(RequestInterface $request)
    {
        $this->beConstructedWith(null, null, 'get');

        $request->getMethod()->willReturn('post');

        $this->matches($request)->shouldReturn(false);
    }

    public function it_matches_a_scheme(RequestInterface $request, UriInterface $uri)
    {
        $this->beConstructedWith(null, null, null, 'http');

        $request->getUri()->willReturn($uri);
        $uri->getScheme()->willReturn('http');

        $this->matches($request)->shouldReturn(true);
    }

    public function it_does_not_match_a_scheme(RequestInterface $request, UriInterface $uri)
    {
        $this->beConstructedWith(null, null, null, 'http');

        $request->getUri()->willReturn($uri);
        $uri->getScheme()->willReturn('https');

        $this->matches($request)->shouldReturn(false);
    }
}
