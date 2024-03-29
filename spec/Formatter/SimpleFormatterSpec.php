<?php

namespace spec\Http\Message\Formatter;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class SimpleFormatterSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Formatter\SimpleFormatter');
    }

    public function it_is_a_formatter()
    {
        $this->shouldImplement('Http\Message\Formatter');
    }

    public function it_formats_the_request(RequestInterface $request, UriInterface $uri)
    {
        $uri->__toString()->willReturn('http://foo.com/bar');
        $request->getMethod()->willReturn('GET');
        $request->getUri()->willReturn($uri);
        $request->getProtocolVersion()->willReturn('1.1');

        $this->formatRequest($request)->shouldReturn('GET http://foo.com/bar 1.1');
    }

    public function it_formats_the_response(ResponseInterface $response, RequestInterface $request)
    {
        $response->getReasonPhrase()->willReturn('OK');
        $response->getProtocolVersion()->willReturn('1.1');
        $response->getStatusCode()->willReturn('200');

        $this->formatResponse($response)->shouldReturn('200 OK 1.1');
        $this->formatResponseForRequest($response, $request)->shouldReturn('200 OK 1.1');
    }
}
