<?php

namespace spec\Http\Message\Encoding;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\StreamInterface;

class DeflateStreamSpec extends ObjectBehavior
{
    use StreamBehavior;

    public function let(StreamInterface $stream)
    {
        $this->beConstructedWith($stream);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Encoding\DeflateStream');
    }

    public function it_reads()
    {
        $stream = new MemoryStream('This is a test stream');
        $this->beConstructedWith($stream);

        $stream->rewind();
        $this->read(4)->shouldReturn(substr(gzdeflate('This is a test stream'), 0, 4));
    }

    public function it_gets_content()
    {
        $stream = new MemoryStream('This is a test stream');
        $this->beConstructedWith($stream);

        $stream->rewind();
        $this->getContents()->shouldReturn(gzdeflate('This is a test stream'));
    }

    public function it_does_not_know_the_content_size()
    {
        $stream = new MemoryStream('This stream is a test stream');
        $this->beConstructedWith($stream);

        $this->getSize()->shouldReturn(null);
    }
}
