<?php

namespace spec\Http\Message\Decorator;

use Http\Message\Decorator\MessageDecorator;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class MessageDecoratorSpec extends ObjectBehavior
{
    public function let(MessageInterface $message)
    {
        $this->beAnInstanceOf('spec\Http\Message\Decorator\MessageDecoratorStub', [$message]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('spec\Http\Message\Decorator\MessageDecoratorStub');
    }

    public function it_is_a_message()
    {
        $this->shouldImplement('Psr\Http\Message\MessageInterface');
    }

    public function it_is_a_message_decorator()
    {
        $this->shouldUseTrait('Http\Message\Decorator\MessageDecorator');
    }

    public function it_has_a_message()
    {
        $this->getMessage()->shouldImplement('Psr\Http\Message\MessageInterface');
    }

    public function it_has_a_protocol_version(MessageInterface $message)
    {
        $message->getProtocolVersion()->willReturn('1.1');

        $this->getProtocolVersion()->shouldReturn('1.1');
    }

    public function it_accepts_a_protocol_version(MessageInterface $message, MessageInterface $newMessage)
    {
        $message->withProtocolVersion('1.1')->willReturn($newMessage);

        $new = $this->withProtocolVersion('1.1');
        $new->getMessage()->shouldReturn($newMessage);
    }

    public function it_has_headers(MessageInterface $message)
    {
        $headers = [
            'Content-Type' => 'application/xml',
        ];

        $message->getHeaders()->willReturn($headers);

        $this->getHeaders()->shouldReturn($headers);
    }

    public function it_can_check_a_header(MessageInterface $message)
    {
        $message->hasHeader('Content-Type')->willReturn(true);

        $this->hasHeader('Content-Type')->shouldReturn(true);
    }

    public function it_has_a_header(MessageInterface $message)
    {
        $message->getHeader('Content-Type')->willReturn(['application/xml']);

        $this->getHeader('Content-Type')->shouldReturn(['application/xml']);
    }

    public function it_has_a_header_line(MessageInterface $message)
    {
        $message->getHeaderLine('Accept-Encoding')->willReturn('gzip, deflate');

        $this->getHeaderLine('Accept-Encoding')->shouldReturn('gzip, deflate');
    }

    public function it_accepts_a_header(MessageInterface $message, MessageInterface $newMessage)
    {
        $message->withHeader('Content-Type', 'application/xml')->willReturn($newMessage);

        $new = $this->withHeader('Content-Type', 'application/xml');
        $new->getMessage()->shouldReturn($newMessage);
    }

    public function it_accepts_added_headers(MessageInterface $message, MessageInterface $newMessage)
    {
        $message->withAddedHeader('Content-Type', 'application/xml')->willReturn($newMessage);

        $new = $this->withAddedHeader('Content-Type', 'application/xml');
        $new->getMessage()->shouldReturn($newMessage);
    }

    public function it_removes_a_header(MessageInterface $message, MessageInterface $newMessage)
    {
        $message->withoutHeader('Content-Type')->willReturn($newMessage);

        $new = $this->withoutHeader('Content-Type');
        $new->getMessage()->shouldReturn($newMessage);
    }

    public function it_has_a_body(MessageInterface $message, StreamInterface $body)
    {
        $message->getBody()->willReturn($body);

        $this->getBody()->shouldReturn($body);
    }

    public function it_accepts_a_body(MessageInterface $message, MessageInterface $newMessage, StreamInterface $body)
    {
        $message->withBody($body)->willReturn($newMessage);

        $new = $this->withBody($body);
        $new->getMessage()->shouldReturn($newMessage);
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

class MessageDecoratorStub implements MessageInterface
{
    use MessageDecorator;

    public function __construct(MessageInterface $message)
    {
        $this->message = $message;
    }
}
