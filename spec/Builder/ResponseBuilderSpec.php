<?php

namespace spec\Http\Message\Builder;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;

class ResponseBuilderSpec extends ObjectBehavior
{
    function it_is_initializable(ResponseInterface $response)
    {
        $this->beConstructedWith($response);
        $this->shouldHaveType('Http\Message\Builder\ResponseBuilder');
    }

    function it_reads_headers_from_array(ResponseInterface $response)
    {
        $response->withStatus(200, 'OK')->willReturn($response);
        $response->withProtocolVersion('1.1')->willReturn($response);
        $response->hasHeader('Content-type')->willReturn(false);
        $response->withHeader('Content-type', 'text/html')->willReturn($response);
        $this->beConstructedWith($response);
        $this->setHeadersFromArray(['HTTP/1.1 200 OK', 'Content-type: text/html']);
    }

    /**
     * @link https://github.com/php-http/message/issues/41
     */
    function it_splits_headers_correctly(ResponseInterface $response)
    {
        $response->withStatus(200, 'OK')->willReturn($response);
        $response->withProtocolVersion('1.1')->willReturn($response);
        $response->hasHeader('Content-type')->willReturn(false);
        $response->withHeader('Content-type', 'application/xml+atom')->willReturn($response);
        $this->beConstructedWith($response);
        $this->setHeadersFromString("HTTP/1.1 200 OK\r\nContent-type: application/xml+atom\r\n");
    }
}
