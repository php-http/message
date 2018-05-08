<?php

namespace spec\Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;
use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;

class CompressStreamSpec extends ObjectBehavior
{
    use StreamBehavior, ZlibStreamBehavior;

    function let(StreamInterface $stream)
    {
        $this->beConstructedWith($stream);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Encoding\CompressStream');
    }

    function it_reads()
    {
        $stream = new MemoryStream('This is a test stream');
        $this->beConstructedWith($stream);

        $stream->rewind();
        $this->read(4)->shouldReturn(substr(gzcompress('This is a test stream'), 0, 4));
    }

    function it_gets_content()
    {
        $stream = new MemoryStream('This is a test stream');
        $this->beConstructedWith($stream);

        $stream->rewind();
        $this->getContents()->shouldReturn(gzcompress('This is a test stream'));
    }

    function it_does_not_know_the_content_size()
    {
        $stream = new MemoryStream('This is a test stream');
        $this->beConstructedWith($stream);

        $stream->rewind();
        $this->getSize()->shouldReturn(null);
    }
}
