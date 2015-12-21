<?php

namespace spec\Http\Message\Authentication;

trait AuthenticationBehavior
{
    function it_is_an_authentication()
    {
        $this->shouldImplement('Http\Message\Authentication');
    }
}
