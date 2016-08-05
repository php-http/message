<?php

namespace spec\Http\Message\Stream;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\StreamInterface;

class BufferedStreamSpec extends ObjectBehavior
{
    public function let(StreamInterface $stream)
    {
        $this->beConstructedWith($stream);

        $stream->getSize()->willReturn(null);
    }

    public function it_is_castable_to_string(StreamInterface $stream)
    {
        $eofCounter = 0;
        $stream->eof()->will(function () use(&$eofCounter) {
            return (++$eofCounter > 1);
        });

        $stream->read(8192)->willReturn('foo');

        $this->__toString()->shouldReturn('foo');
    }

    public function it_detachs(StreamInterface $stream)
    {
        $stream->eof()->willReturn(true);
        $stream->read(8192)->willReturn('');
        $stream->close()->shouldBeCalled();

        $this->detach()->shouldBeResource();
        $this->detach()->shouldBeNull();
    }

    public function it_gets_size(StreamInterface $stream)
    {
        $stream->eof()->willReturn(false);
        $this->getSize()->shouldReturn(null);

        $eofCounter = 0;
        $stream->eof()->will(function () use(&$eofCounter) {
            return (++$eofCounter > 1);
        });

        $stream->read(8192)->willReturn('foo');

        $this->getContents()->shouldReturn('foo');
        $this->getSize()->shouldReturn(3);
    }

    public function it_tells(StreamInterface $stream)
    {
        $this->tell()->shouldReturn(0);

        $eofCounter = 0;
        $stream->eof()->will(function () use(&$eofCounter) {
            return (++$eofCounter > 1);
        });

        $stream->read(8192)->willReturn('foo');
        $this->getContents()->shouldReturn('foo');
        $this->tell()->shouldReturn(3);
    }

    public function it_eof(StreamInterface $stream)
    {
        // Case when underlying is false
        $stream->eof()->willReturn(false);
        $this->eof()->shouldReturn(false);

        // Case when sync and underlying is true
        $stream->eof()->willReturn(true);
        $this->eof()->shouldReturn(true);

        // Case not sync but underlying is true
        $eofCounter = 0;
        $stream->eof()->will(function () use(&$eofCounter) {
            return (++$eofCounter > 1);
        });

        $stream->read(8192)->willReturn('foo');

        $this->getContents()->shouldReturn('foo');
        $this->seek(0);

        $stream->eof()->willReturn(true);
        $this->eof()->shouldReturn(false);
    }

    public function it_is_seekable(StreamInterface $stream)
    {
        $this->isSeekable()->shouldReturn(true);
    }

    public function it_seeks(StreamInterface $stream)
    {
        $this->seek(0);
        $this->tell()->shouldReturn(0);

        $eofCounter = 0;
        $stream->eof()->will(function () use(&$eofCounter) {
            return (++$eofCounter > 1);
        });

        $stream->read(8192)->willReturn('foo');

        $this->getContents()->shouldReturn('foo');
        $this->seek(2);
        $this->tell()->shouldReturn(2);
    }

    public function it_rewinds(StreamInterface $stream)
    {
        $this->rewind();
        $this->tell()->shouldReturn(0);

        $eofCounter = 0;
        $stream->eof()->will(function () use(&$eofCounter) {
            return (++$eofCounter > 1);
        });

        $stream->read(8192)->willReturn('foo');

        $this->getContents()->shouldReturn('foo');
        $this->tell()->shouldReturn(3);
        $this->rewind();
        $this->tell()->shouldReturn(0);
    }

    public function it_is_not_writable(StreamInterface $stream)
    {
        $this->isWritable()->shouldReturn(false);
        $this->shouldThrow('\RuntimeException')->duringWrite('foo');
    }

    public function it_is_readable(StreamInterface $stream)
    {
        $this->isReadable()->shouldReturn(true);
    }

    public function it_reads(StreamInterface $stream)
    {
        $eofCounter = 0;
        $stream->eof()->will(function () use(&$eofCounter) {
            return (++$eofCounter > 1);
        });

        $stream->read(3)->willReturn('foo');
        $this->read(3)->shouldReturn('foo');

        $stream->read(3)->willReturn('bar');
        $this->read(3)->shouldReturn('bar');

        $this->rewind();
        $this->read(4)->shouldReturn('foob');

        $stream->read(3)->willReturn('baz');
        $this->read(5)->shouldReturn('arbaz');
    }

    public function it_get_contents(StreamInterface $stream)
    {
        $eofCounter = 0;
        $stream->eof()->will(function () use(&$eofCounter) {
            return (++$eofCounter > 1);
        });

        $stream->read(8192)->willReturn('foo');

        $this->getContents()->shouldReturn('foo');
        $this->eof()->shouldReturn(true);
    }

    public function it_get_metadatas(StreamInterface $stream)
    {
        $this->getMetadata()->shouldBeArray();
        $this->getMetadata('unexistant')->shouldBeNull();
        $this->getMetadata('stream_type')->shouldReturn('TEMP');
    }
}
