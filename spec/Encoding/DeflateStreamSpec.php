<?php

namespace spec\Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;
use PhpSpec\ObjectBehavior;

class DeflateStreamSpec extends ObjectBehavior
{
    use StreamBehavior;

    function let(StreamInterface $stream)
    {
        $this->beConstructedWith($stream);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Encoding\DeflateStream');
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

    function it_does_not_know_the_content_size()
    {
        $stream = new MemoryStream('This stream is a test stream');
        $this->beConstructedWith($stream);

        $this->getSize()->shouldReturn(null);
    }
}
