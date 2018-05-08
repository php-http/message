<?php

namespace spec\Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;
use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;

class DecompressStreamSpec extends ObjectBehavior
{
    use StreamBehavior, ZlibStreamBehavior;

    function let(StreamInterface $stream)
    {
        $this->beConstructedWith($stream);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Encoding\DecompressStream');
    }

    function it_reads()
    {
        // "This is a test stream" | deflate
        $stream = new MemoryStream(gzcompress('This is a test stream'));
        $this->beConstructedWith($stream);

        $this->read(4)->shouldReturn('This');
    }

    function it_gets_content()
    {
        // "This is a test stream" | deflate
        $stream = new MemoryStream(gzcompress('This is a test stream'));
        $this->beConstructedWith($stream);

        $this->getContents()->shouldReturn('This is a test stream');
    }

    function it_does_not_know_the_content_size()
    {
        $stream = new MemoryStream(gzcompress('This is a test stream'));
        $this->beConstructedWith($stream);

        $this->getSize()->shouldReturn(null);
    }
}
