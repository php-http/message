<?php

namespace spec\Http\Message\Decorator;

use Http\Message\Decorator\RequestDecorator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use PhpSpec\ObjectBehavior;

class RequestDecoratorSpec extends ObjectBehavior
{
    function let(RequestInterface $request)
    {
        $this->beAnInstanceOf('spec\Http\Message\Decorator\RequestDecoratorStub', [$request]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('spec\Http\Message\Decorator\RequestDecoratorStub');
    }

    function it_is_a_request()
    {
        $this->shouldImplement('Psr\Http\Message\RequestInterface');
    }

    function it_is_a_request_decorator()
    {
        $this->shouldUseTrait('Http\Message\Decorator\RequestDecorator');
    }

    function it_has_a_request()
    {
        $this->getRequest()->shouldImplement('Psr\Http\Message\RequestInterface');
    }

    function it_accepts_a_request(RequestInterface $request)
    {
        $new = $this->withRequest($request);

        $new->getRequest()->shouldReturn($request);
    }

    function it_has_a_request_target(RequestInterface $request)
    {
        $request->getRequestTarget()->willReturn('/');

        $this->getRequestTarget()->shouldReturn('/');
    }

    function it_accepts_a_request_target(RequestInterface $request, RequestInterface $newRequest)
    {
        $request->withRequestTarget('/')->willReturn($newRequest);

        $new = $this->withRequestTarget('/');
        $new->getMessage()->shouldReturn($newRequest);
    }

    function it_has_a_method(RequestInterface $request)
    {
        $request->getMethod()->willReturn('GET');

        $this->getMethod()->shouldReturn('GET');
    }

    function it_accepts_a_method(RequestInterface $request, RequestInterface $newRequest)
    {
        $request->withMethod('GET')->willReturn($newRequest);

        $new = $this->withMethod('GET');
        $new->getMessage()->shouldReturn($newRequest);
    }

    function it_has_an_uri(RequestInterface $request, UriInterface $uri)
    {
        $request->getUri()->willReturn($uri);

        $this->getUri()->shouldReturn($uri);
    }

    function it_accepts_an_uri(RequestInterface $request, RequestInterface $newRequest, UriInterface $uri)
    {
        $request->withUri($uri, false)->willReturn($newRequest);

        $new = $this->withUri($uri);
        $new->getMessage()->shouldReturn($newRequest);
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

class RequestDecoratorStub implements RequestInterface
{
    use RequestDecorator;

    public function __construct(RequestInterface $request)
    {
        $this->message = $request;
    }
}
