<?php

namespace spec\Http\Message\Encoding;

use Http\Message\Encoding\FilteredStream;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\StreamInterface;

class FilteredStreamStubSpec extends ObjectBehavior
{
    function it_throws_during_instantiation_with_invalid_read_filter_options(StreamInterface $stream)
    {
        $this->beAnInstanceOf('spec\Http\Message\Encoding\FilteredStreamStub');
        $this->beConstructedWith($stream, 'foo');
        $this->shouldThrow('RuntimeException')->duringInstantiation();
    }

    function it_throws_during_instantiation_with_invalid_write_filter_options(StreamInterface $stream)
    {
        $this->beAnInstanceOf('spec\Http\Message\Encoding\FilteredStreamStub');
        $this->beConstructedWith($stream, null, 'foo');
        $this->shouldThrow('RuntimeException')->duringInstantiation();
    }

    function let(StreamInterface $stream)
    {
        $this->beAnInstanceOf('spec\Http\Message\Encoding\FilteredStreamStub');
        $this->beConstructedWith($stream);
    }

    function it_reads()
    {
        // "This is a test stream" | base64_encode
        $stream = new MemoryStream('This is a test stream');
        $this->beConstructedWith($stream);

        $this->read(4)->shouldReturn('VGhp');
    }

    function it_gets_content()
    {
        // "This is a test stream" | base64_encode
        $stream = new MemoryStream('This is a test stream');
        $this->beConstructedWith($stream);

        $this->getContents()->shouldReturn('VGhpcyBpcyBhIHRlc3Qgc3RyZWFt');
    }

    function it_does_not_know_the_content_size()
    {
        $stream = new MemoryStream(gzencode('This is a test stream'));
        $this->beConstructedWith($stream);

        $this->getSize()->shouldBeNull();
    }
}

class FilteredStreamStub extends FilteredStream
{
    protected function readFilter()
    {
        return 'convert.base64-encode';
    }

    protected function writeFilter()
    {
        return 'convert.base64-encode';
    }
}
