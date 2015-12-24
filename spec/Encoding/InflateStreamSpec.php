<?php

namespace spec\Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;
use PhpSpec\ObjectBehavior;

class InflateStreamSpec extends ObjectBehavior
{
    use StreamBehavior, ZlibStreamBehavior;

    function let(StreamInterface $stream)
    {
        $this->beConstructedWith($stream);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Encoding\InflateStream');
    }

    function it_reads()
    {
        // "This is a test stream" | deflate
        $stream = new MemoryStream(gzdeflate('This is a test stream'));
        $this->beConstructedWith($stream);

        $this->read(4)->shouldReturn('This');
    }

    function it_gets_content()
    {
        // "This is a test stream" | deflate
        $stream = new MemoryStream(gzdeflate('This is a test stream'));
        $this->beConstructedWith($stream);

        $this->getContents()->shouldReturn('This is a test stream');
    }
}
