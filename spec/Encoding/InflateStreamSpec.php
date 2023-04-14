<?php

namespace spec\Http\Message\Encoding;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\StreamInterface;

class InflateStreamSpec extends ObjectBehavior
{
    use StreamBehavior;
    use ZlibStreamBehavior;

    public function let(StreamInterface $stream)
    {
        $this->beConstructedWith($stream);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Encoding\InflateStream');
    }

    public function it_reads()
    {
        // "This is a test stream" | deflate
        $stream = new MemoryStream(gzdeflate('This is a test stream'));
        $this->beConstructedWith($stream);

        $this->read(4)->shouldReturn('This');
    }

    public function it_gets_content()
    {
        // "This is a test stream" | deflate
        $stream = new MemoryStream(gzdeflate('This is a test stream'));
        $this->beConstructedWith($stream);

        $this->getContents()->shouldReturn('This is a test stream');
    }

    public function it_does_not_know_the_content_size()
    {
        $stream = new MemoryStream(gzdeflate('This stream is a test stream'));
        $this->beConstructedWith($stream);

        $this->getSize()->shouldReturn(null);
    }
}
