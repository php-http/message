<?php

namespace spec\Http\Message\Formatter;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use PhpSpec\ObjectBehavior;

class SimpleFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Formatter\SimpleFormatter');
    }

    function it_is_a_formatter()
    {
        $this->shouldImplement('Http\Message\Formatter');
    }

    function it_formats_the_request(RequestInterface $request, UriInterface $uri)
    {
        $uri->__toString()->willReturn('http://foo.com/bar');
        $request->getMethod()->willReturn('GET');
        $request->getUri()->willReturn($uri);
        $request->getProtocolVersion()->willReturn('1.1');

        $this->formatRequest($request)->shouldReturn('GET http://foo.com/bar 1.1');
    }

    function it_formats_the_response(ResponseInterface $response)
    {
        $response->getReasonPhrase()->willReturn('OK');
        $response->getProtocolVersion()->willReturn('1.1');
        $response->getStatusCode()->willReturn('200');

        $this->formatResponse($response)->shouldReturn('200 OK 1.1');
    }
}
