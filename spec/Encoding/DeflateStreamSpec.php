<?php

namespace spec\Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;
use PhpSpec\ObjectBehavior;

class DeflateStreamSpec extends ObjectBehavior
{
    function let(StreamInterface $stream)
    {
        $this->beConstructedWith($stream);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Encoding\DeflateStream');
    }

    function it_is_a_stream()
    {
        $this->shouldImplement('Psr\Http\Message\StreamInterface');
    }

    function it_reads()
    {
        $stream = new MemoryStream('This is a test stream');
        $this->beConstructedWith($stream);

        $stream->rewind();
        $this->read(4)->shouldReturn(substr(gzdeflate('This is a test stream'),0, 4));
    }

    function it_gets_content()
    {
        $stream = new MemoryStream('This is a test stream');
        $this->beConstructedWith($stream);

        $stream->rewind();
        $this->getContents()->shouldReturn(gzdeflate('This is a test stream'));
    }
}
