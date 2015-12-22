<?php

namespace spec\Http\Message\Encoding;

trait StreamBehavior
{
    function it_is_a_stream()
    {
        $this->shouldImplement('Psr\Http\Message\StreamInterface');
    }
}
