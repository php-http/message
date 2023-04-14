<?php

namespace spec\Http\Message\Encoding;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\StreamInterface;

class ChunkStreamSpec extends ObjectBehavior
{
    use StreamBehavior;

    public function let(StreamInterface $stream)
    {
        $this->beConstructedWith($stream);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Encoding\ChunkStream');
    }

    public function it_chunks_content()
    {
        $stream = new MemoryStream('This is a stream');
        $this->beConstructedWith($stream, 6);

        $this->getContents()->shouldReturn("10\r\nThis is a stream\r\n0\r\n\r\n");
    }

    public function it_chunks_in_multiple()
    {
        $stream = new MemoryStream('This is a stream', 6);
        $this->beConstructedWith($stream, 6);

        $this->getContents()->shouldReturn("6\r\nThis i\r\n6\r\ns a st\r\n4\r\nream\r\n0\r\n\r\n");
    }

    public function it_does_not_know_the_content_size()
    {
        $stream = new MemoryStream('This is a stream');
        $this->beConstructedWith($stream, 6);

        $this->getSize()->shouldReturn(null);
    }
}
