<?php

namespace spec\Http\Message\StreamFactory;

use GuzzleHttp\Psr7\Stream;
use PhpSpec\ObjectBehavior;

class GuzzleStreamFactorySpec extends ObjectBehavior
{
    use StreamFactoryBehavior;

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\StreamFactory\GuzzleStreamFactory');
    }

    public function it_creates_a_stream_from_stream()
    {
        $this->createStream(new Stream(fopen('php://memory', 'rw')))
            ->shouldHaveType('Psr\Http\Message\StreamInterface');
    }
}
