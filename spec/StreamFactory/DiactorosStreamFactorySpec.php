<?php

namespace spec\Http\Message\StreamFactory;

use Laminas\Diactoros\Stream;
use PhpSpec\ObjectBehavior;

class DiactorosStreamFactorySpec extends ObjectBehavior
{
    use StreamFactoryBehavior;

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\StreamFactory\DiactorosStreamFactory');
    }

    public function it_creates_a_stream_from_stream()
    {
        $this->createStream(new Stream('php://memory'))
            ->shouldHaveType('Psr\Http\Message\StreamInterface');
    }
}
