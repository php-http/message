<?php

namespace spec\Http\Message\Decorator;

use Http\Message\Decorator\MessageDecorator;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use PhpSpec\ObjectBehavior;

class MessageDecoratorSpec extends ObjectBehavior
{
    function let(MessageInterface $message)
    {
        $this->beAnInstanceOf('spec\Http\Message\Decorator\MessageDecoratorStub', [$message]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('spec\Http\Message\Decorator\MessageDecoratorStub');
    }

    function it_is_a_message()
    {
        $this->shouldImplement('Psr\Http\Message\MessageInterface');
    }

    function it_is_a_message_decorator()
    {
        $this->shouldUseTrait('Http\Message\Decorator\MessageDecorator');
    }

    function it_has_a_message()
    {
        $this->getMessage()->shouldImplement('Psr\Http\Message\MessageInterface');
    }

    function it_has_a_protocol_version(MessageInterface $message)
    {
        $message->getProtocolVersion()->willReturn('1.1');

        $this->getProtocolVersion()->shouldReturn('1.1');
    }

    function it_accepts_a_protocol_version(MessageInterface $message, MessageInterface $newMessage)
    {
        $message->withProtocolVersion('1.1')->willReturn($newMessage);

        $new = $this->withProtocolVersion('1.1');
        $new->getMessage()->shouldReturn($newMessage);
    }

    function it_has_headers(MessageInterface $message)
    {
        $headers = [
            'Content-Type' => 'application/xml'
        ];

        $message->getHeaders()->willReturn($headers);

        $this->getHeaders()->shouldReturn($headers);
    }

    function it_can_check_a_header(MessageInterface $message)
    {
        $message->hasHeader('Content-Type')->willReturn(true);

        $this->hasHeader('Content-Type')->shouldReturn(true);
    }

    function it_has_a_header(MessageInterface $message)
    {
        $message->getHeader('Content-Type')->willReturn('application/xml');

        $this->getHeader('Content-Type')->shouldReturn('application/xml');
    }

    function it_has_a_header_line(MessageInterface $message)
    {
        $message->getHeaderLine('Accept-Encoding')->willReturn('gzip, deflate');

        $this->getHeaderLine('Accept-Encoding')->shouldReturn('gzip, deflate');
    }

    function it_accepts_a_header(MessageInterface $message, MessageInterface $newMessage)
    {
        $message->withHeader('Content-Type', 'application/xml')->willReturn($newMessage);

        $new = $this->withHeader('Content-Type', 'application/xml');
        $new->getMessage()->shouldReturn($newMessage);
    }

    function it_accepts_added_headers(MessageInterface $message, MessageInterface $newMessage)
    {
        $message->withAddedHeader('Content-Type', 'application/xml')->willReturn($newMessage);

        $new = $this->withAddedHeader('Content-Type', 'application/xml');
        $new->getMessage()->shouldReturn($newMessage);
    }

    function it_removes_a_header(MessageInterface $message, MessageInterface $newMessage)
    {
        $message->withoutHeader('Content-Type')->willReturn($newMessage);

        $new = $this->withoutHeader('Content-Type');
        $new->getMessage()->shouldReturn($newMessage);
    }

    function it_has_a_body(MessageInterface $message, StreamInterface $body)
    {
        $message->getBody()->willReturn($body);

        $this->getBody()->shouldReturn($body);
    }

    function it_accepts_a_body(MessageInterface $message, MessageInterface $newMessage, StreamInterface $body)
    {
        $message->withBody($body)->willReturn($newMessage);

        $new = $this->withBody($body);
        $new->getMessage()->shouldReturn($newMessage);
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

class MessageDecoratorStub implements MessageInterface
{
    use MessageDecorator;

    public function __construct(MessageInterface $message)
    {
        $this->message = $message;
    }
}
