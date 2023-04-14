<?php

namespace spec\Http\Message\Decorator;

use Http\Message\Decorator\ResponseDecorator;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;

class ResponseDecoratorSpec extends ObjectBehavior
{
    public function let(ResponseInterface $response)
    {
        $this->beAnInstanceOf('spec\Http\Message\Decorator\ResponseDecoratorStub', [$response]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('spec\Http\Message\Decorator\ResponseDecoratorStub');
    }

    public function it_is_a_response()
    {
        $this->shouldImplement('Psr\Http\Message\ResponseInterface');
    }

    public function it_is_a_response_decorator()
    {
        $this->shouldUseTrait('Http\Message\Decorator\ResponseDecorator');
    }

    public function it_has_a_response()
    {
        $this->getResponse()->shouldImplement('Psr\Http\Message\ResponseInterface');
    }

    public function it_accepts_a_response(ResponseInterface $response)
    {
        $new = $this->withResponse($response);

        $new->getResponse()->shouldReturn($response);
    }

    public function it_has_a_status_code(ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(200);

        $this->getStatusCode()->shouldReturn(200);
    }

    public function it_accepts_a_status(ResponseInterface $response, ResponseInterface $newResponse)
    {
        $response->withStatus(200, 'OK')->willReturn($newResponse);

        $new = $this->withStatus(200, 'OK');
        $new->getMessage()->shouldReturn($newResponse);
    }

    public function it_has_a_reason_phrase(ResponseInterface $response)
    {
        $response->getReasonPhrase()->willReturn('OK');

        $this->getReasonPhrase()->shouldReturn('OK');
    }

    public function getMatchers(): array
    {
        return [
            'useTrait' => function ($subject, $trait) {
                return class_uses($subject, $trait);
            },
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
