<?php

namespace spec\Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;
use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;

class DechunkStreamSpec extends ObjectBehavior
{
    use StreamBehavior;

    function let(StreamInterface $stream)
    {
        $this->beConstructedWith($stream);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Encoding\DechunkStream');
    }

    function it_reads()
    {
        $stream = new MemoryStream("4\r\ntest\r\n4\r\ntest\r\n0\r\n\r\n\0");
        $this->beConstructedWith($stream);

        $this->read(6)->shouldReturn('testte');
        $this->read(6)->shouldReturn('st');
    }

    function it_gets_content()
    {
        $stream = new MemoryStream("4\r\ntest\r\n0\r\n\r\n\0");
        $this->beConstructedWith($stream);

        $this->getContents()->shouldReturn('test');
    }

        function it_does_not_know_the_content_size()
    {
        $stream = new MemoryStream("4\r\ntest\r\n4\r\ntest\r\n0\r\n\r\n\0");
        $this->beConstructedWith($stream);

        $this->getSize()->shouldReturn(null);
    }
}
