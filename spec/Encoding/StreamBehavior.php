<?php

namespace spec\Http\Message\Encoding;

trait StreamBehavior
{
    public function it_is_a_stream()
    {
        $this->shouldImplement('Psr\Http\Message\StreamInterface');
    }
}
