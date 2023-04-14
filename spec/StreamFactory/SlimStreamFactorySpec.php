<?php

namespace spec\Http\Message\StreamFactory;

use PhpSpec\ObjectBehavior;
use Slim\Http\Stream;

/**
 * @require Slim\Http\Stream
 */
class SlimStreamFactorySpec extends ObjectBehavior
{
    use StreamFactoryBehavior;

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\StreamFactory\SlimStreamFactory');
    }

    public function it_creates_a_stream_from_stream()
    {
        $resource = fopen('php://memory', 'rw');
        $this->createStream(new Stream($resource))
            ->shouldHaveType('Psr\Http\Message\StreamInterface');
    }
}
