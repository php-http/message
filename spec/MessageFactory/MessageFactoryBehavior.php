<?php

namespace spec\Http\Message\MessageFactory;

trait MessageFactoryBehavior
{
    function it_is_a_message_factory()
    {
        $this->shouldImplement('Http\Message\MessageFactory');
    }

    function it_creates_a_request()
    {
        $request = $this->createRequest('GET', '/');

        $request->shouldHaveType('Psr\Http\Message\RequestInterface');
        $request->getMethod()->shouldReturn('GET');
        $request->getRequestTarget()->shouldReturn('/');
    }

    function it_creates_a_response()
    {
        $response = $this->createResponse();

        $response->shouldHaveType('Psr\Http\Message\ResponseInterface');
    }
}
