<?php

namespace spec\Http\Message\StreamFactory;

trait StreamFactoryBehavior
{
    function it_is_a_stream_factory()
    {
        $this->shouldImplement('Http\Message\StreamFactory');
    }

    function it_creates_a_stream_from_string()
    {
        $this->createStream('foo')->shouldHaveType('Psr\Http\Message\StreamInterface');
    }

    function it_creates_a_stream_from_resource()
    {
        $this->createStream(fopen('php://memory', 'rw'))
            ->shouldHaveType('Psr\Http\Message\StreamInterface');
    }

    function it_creates_a_stream_from_null()
    {
        $this->createStream(null)->shouldHaveType('Psr\Http\Message\StreamInterface');
    }

    function it_creates_a_stream_from_non_seekable_resource()
    {
        $url = 'https://raw.githubusercontent.com/php-http/multipart-stream-builder/master/tests/Resources/httplug.png';
        $resource = fopen($url, 'r');
        $this->createStream($resource)
            ->shouldHaveType('Psr\Http\Message\StreamInterface');
    }
}
