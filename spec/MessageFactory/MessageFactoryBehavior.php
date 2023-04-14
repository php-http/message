<?php

namespace spec\Http\Message\MessageFactory;

trait MessageFactoryBehavior
{
    public function it_is_a_message_factory()
    {
        $this->shouldImplement('Http\Message\MessageFactory');
    }

    public function it_creates_a_request()
    {
        $request = $this->createRequest('GET', '/', ['X-hello' => 'world'], 'lorem');

        $request->shouldHaveType('Psr\Http\Message\RequestInterface');
        $request->getMethod()->shouldReturn('GET');
        $request->getRequestTarget()->shouldReturn('/');
        $request->getBody()->__toString()->shouldReturn('lorem');
        $request->getHeaderLine('X-hello')->shouldReturn('world');
    }

    public function it_creates_a_response()
    {
        $response = $this->createResponse();

        $response->shouldHaveType('Psr\Http\Message\ResponseInterface');
    }
}
