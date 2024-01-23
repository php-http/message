<?php

namespace spec\Http\Message\Encoding;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\StreamInterface;

class DechunkStreamSpec extends ObjectBehavior
{
    use StreamBehavior;

    public function let(StreamInterface $stream)
    {
        $this->beConstructedWith($stream);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Encoding\DechunkStream');
    }

    public function it_reads()
    {
        $stream = new MemoryStream("4\r\ntest\r\n4\r\ntest\r\n0\r\n\r\n\0");
        $this->beConstructedWith($stream);

        $this->read(6)->shouldReturn('testte');
        $this->read(6)->shouldReturn('st');
    }

    public function it_gets_content()
    {
        $stream = new MemoryStream("4\r\ntest\r\n0\r\n\r\n\0");
        $this->beConstructedWith($stream);

        $this->getContents()->shouldReturn('test');
    }

    public function it_does_not_know_the_content_size()
    {
        $stream = new MemoryStream("4\r\ntest\r\n4\r\ntest\r\n0\r\n\r\n\0");
        $this->beConstructedWith($stream);

        $this->getSize()->shouldReturn(null);
    }
}
