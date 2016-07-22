<?php

namespace spec\Http\Message;

use Http\Message\MemoryClonedStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\MessageInterface;
use spec\Http\Message\Encoding\MemoryStream;

class MessageClonerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\MessageCloner');
    }

    public function it_clone_a_message(MessageInterface $message, MessageInterface $messageCloned)
    {
        $stream = new MemoryStream('test');
        $message->getBody()->willReturn($stream);
        $message->withBody(Argument::type(MemoryClonedStream::class))->willReturn($messageCloned);

        $this->cloneMessage($message)->shouldEqual($messageCloned);
    }
}
