<?php

namespace spec\Http\Message\Formatter;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class FullHttpMessageFormatterSpec extends ObjectBehavior
{
    function let($maxBodyLength)
    {
        $this->beConstructedWith($maxBodyLength);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Formatter\FullHttpMessageFormatter');
    }

    function it_is_a_formatter()
    {
        $this->shouldImplement('Http\Message\Formatter');
    }

    function it_formats_the_request_with_size_limit(RequestInterface $request, StreamInterface $stream)
    {
        $this->beConstructedWith(18);

        $stream->isSeekable()->willReturn(true);
        $stream->rewind()->shouldBeCalled();
        $stream->__toString()->willReturn('This is an HTML stream request content.');
        $request->getBody()->willReturn($stream);
        $request->getMethod()->willReturn('GET');
        $request->getRequestTarget()->willReturn('/foo');
        $request->getProtocolVersion()->willReturn('1.1');
        $request->getHeaders()->willReturn([
            'X-Param-Foo' => ['foo'],
            'X-Param-Bar' => ['bar'],
        ]);

        $expectedMessage = <<<STR
GET /foo HTTP/1.1
X-Param-Foo: foo
X-Param-Bar: bar

This is an HTML st
STR;
        $this->formatRequest($request)->shouldReturn($expectedMessage);
    }

    function it_formats_the_request_without_size_limit(RequestInterface $request, StreamInterface $stream)
    {
        $this->beConstructedWith(null);

        $stream->isSeekable()->willReturn(true);
        $stream->rewind()->shouldBeCalled();
        $stream->__toString()->willReturn('This is an HTML stream request content.');
        $request->getBody()->willReturn($stream);
        $request->getMethod()->willReturn('GET');
        $request->getRequestTarget()->willReturn('/foo');
        $request->getProtocolVersion()->willReturn('1.1');
        $request->getHeaders()->willReturn([
            'X-Param-Foo' => ['foo'],
            'X-Param-Bar' => ['bar'],
        ]);

        $expectedMessage = <<<STR
GET /foo HTTP/1.1
X-Param-Foo: foo
X-Param-Bar: bar

This is an HTML stream request content.
STR;
        $this->formatRequest($request)->shouldReturn($expectedMessage);
    }

    function it_does_not_format_the_request(RequestInterface $request, StreamInterface $stream)
    {
        $this->beConstructedWith(0);

        $stream->isSeekable()->willReturn(true);
        $stream->__toString()->willReturn('This is an HTML stream request content.');
        $request->getBody()->willReturn($stream);
        $request->getMethod()->willReturn('GET');
        $request->getRequestTarget()->willReturn('/foo');
        $request->getProtocolVersion()->willReturn('1.1');
        $request->getHeaders()->willReturn([
            'X-Param-Foo' => ['foo'],
            'X-Param-Bar' => ['bar'],
        ]);

        $expectedMessage = <<<STR
GET /foo HTTP/1.1
X-Param-Foo: foo
X-Param-Bar: bar


STR;
        $this->formatRequest($request)->shouldReturn($expectedMessage);
    }

    function it_does_not_format_no_seekable_request(RequestInterface $request, StreamInterface $stream)
    {
        $this->beConstructedWith(1000);

        $stream->isSeekable()->willReturn(false);
        $stream->__toString()->willReturn('This is an HTML stream request content.');
        $request->getBody()->willReturn($stream);
        $request->getMethod()->willReturn('GET');
        $request->getRequestTarget()->willReturn('/foo');
        $request->getProtocolVersion()->willReturn('1.1');
        $request->getHeaders()->willReturn([
            'X-Param-Foo' => ['foo'],
            'X-Param-Bar' => ['bar'],
        ]);

        $expectedMessage = <<<STR
GET /foo HTTP/1.1
X-Param-Foo: foo
X-Param-Bar: bar


STR;
        $this->formatRequest($request)->shouldReturn($expectedMessage);
    }

    function it_formats_the_response_with_size_limit(ResponseInterface $response, StreamInterface $stream)
    {
        $this->beConstructedWith(18);

        $stream->isSeekable()->willReturn(true);
        $stream->rewind()->shouldBeCalled();
        $stream->__toString()->willReturn('This is an HTML stream response content.');
        $response->getBody()->willReturn($stream);
        $response->getProtocolVersion()->willReturn('1.1');
        $response->getStatusCode()->willReturn(200);
        $response->getReasonPhrase()->willReturn('OK');
        $response->getHeaders()->willReturn([
            'X-Param-Foo' => ['foo'],
            'X-Param-Bar' => ['bar'],
        ]);

        $expectedMessage = <<<STR
HTTP/1.1 200 OK
X-Param-Foo: foo
X-Param-Bar: bar

This is an HTML st
STR;
        $this->formatResponse($response)->shouldReturn($expectedMessage);
    }

    function it_formats_the_response_without_size_limit(ResponseInterface $response, StreamInterface $stream)
    {
        $this->beConstructedWith(null);

        $stream->isSeekable()->willReturn(true);
        $stream->rewind()->shouldBeCalled();
        $stream->__toString()->willReturn('This is an HTML stream response content.');
        $response->getBody()->willReturn($stream);
        $response->getProtocolVersion()->willReturn('1.1');
        $response->getStatusCode()->willReturn(200);
        $response->getReasonPhrase()->willReturn('OK');
        $response->getHeaders()->willReturn([
            'X-Param-Foo' => ['foo'],
            'X-Param-Bar' => ['bar'],
        ]);

        $expectedMessage = <<<STR
HTTP/1.1 200 OK
X-Param-Foo: foo
X-Param-Bar: bar

This is an HTML stream response content.
STR;
        $this->formatResponse($response)->shouldReturn($expectedMessage);
    }

    function it_does_not_format_the_response(ResponseInterface $response, StreamInterface $stream)
    {
        $this->beConstructedWith(0);

        $stream->isSeekable()->willReturn(true);
        $stream->__toString()->willReturn('This is an HTML stream response content.');
        $response->getBody()->willReturn($stream);
        $response->getProtocolVersion()->willReturn('1.1');
        $response->getStatusCode()->willReturn(200);
        $response->getReasonPhrase()->willReturn('OK');
        $response->getHeaders()->willReturn([
            'X-Param-Foo' => ['foo'],
            'X-Param-Bar' => ['bar'],
        ]);

        $expectedMessage = <<<STR
HTTP/1.1 200 OK
X-Param-Foo: foo
X-Param-Bar: bar


STR;
        $this->formatResponse($response)->shouldReturn($expectedMessage);
    }

    function it_does_not_format_no_seekable_response(ResponseInterface $response, StreamInterface $stream)
    {
        $this->beConstructedWith(1000);

        $stream->isSeekable()->willReturn(false);
        $stream->__toString()->willReturn('This is an HTML stream response content.');
        $response->getBody()->willReturn($stream);
        $response->getProtocolVersion()->willReturn('1.1');
        $response->getStatusCode()->willReturn(200);
        $response->getReasonPhrase()->willReturn('OK');
        $response->getHeaders()->willReturn([
            'X-Param-Foo' => ['foo'],
            'X-Param-Bar' => ['bar'],
        ]);

        $expectedMessage = <<<STR
HTTP/1.1 200 OK
X-Param-Foo: foo
X-Param-Bar: bar


STR;
        $this->formatResponse($response)->shouldReturn($expectedMessage);
    }

    function it_omits_body_with_null_bytes(RequestInterface $request, StreamInterface $stream)
    {
        $this->beConstructedWith(1);

        $stream->isSeekable()->willReturn(true);
        $stream->rewind()->shouldBeCalled();
        $stream->__toString()->willReturn("\0");
        $request->getBody()->willReturn($stream);
        $request->getMethod()->willReturn('GET');
        $request->getRequestTarget()->willReturn('/foo');
        $request->getProtocolVersion()->willReturn('1.1');
        $request->getHeaders()->willReturn([]);

        $expectedMessage = <<<STR
GET /foo HTTP/1.1

[binary stream omitted]
STR;
        $this->formatRequest($request)->shouldReturn($expectedMessage);
    }
}
