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
}
