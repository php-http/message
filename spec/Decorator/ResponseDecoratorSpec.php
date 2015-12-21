<?php

namespace spec\Http\Message\Decorator;

use Http\Message\Decorator\ResponseDecorator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use PhpSpec\ObjectBehavior;

class ResponseDecoratorSpec extends ObjectBehavior
{
    function let(ResponseInterface $response)
    {
        $this->beAnInstanceOf('spec\Http\Message\Decorator\ResponseDecoratorStub', [$response]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('spec\Http\Message\Decorator\ResponseDecoratorStub');
    }

    function it_is_a_response()
    {
        $this->shouldImplement('Psr\Http\Message\ResponseInterface');
    }

    function it_is_a_response_decorator()
    {
        $this->shouldUseTrait('Http\Message\Decorator\ResponseDecorator');
    }

    function it_has_a_response()
    {
        $this->getResponse()->shouldImplement('Psr\Http\Message\ResponseInterface');
    }

    function it_accepts_a_response(ResponseInterface $response)
    {
        $new = $this->withResponse($response);

        $new->getResponse()->shouldReturn($response);
    }

    function it_has_a_status_code(ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(200);

        $this->getStatusCode()->shouldReturn(200);
    }

    function it_accepts_a_status(ResponseInterface $response, ResponseInterface $newResponse)
    {
        $response->withStatus(200, 'OK')->willReturn($newResponse);

        $new = $this->withStatus(200, 'OK');
        $new->getMessage()->shouldReturn($newResponse);
    }

    function it_has_a_reason_phrase(ResponseInterface $response)
    {
        $response->getReasonPhrase()->willReturn('OK');

        $this->getReasonPhrase()->shouldReturn('OK');
    }

    function getMatchers()
    {
        return [
            'useTrait' => function ($subject, $trait) {
                return class_uses($subject, $trait);
            }
        ];
    }
}

class ResponseDecoratorStub implements ResponseInterface
{
    use ResponseDecorator;

    public function __construct(ResponseInterface $response)
    {
        $this->message = $response;
    }
}
