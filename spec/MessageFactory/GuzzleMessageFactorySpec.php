<?php

namespace spec\Http\Message\MessageFactory;

use PhpSpec\ObjectBehavior;

class GuzzleMessageFactorySpec extends ObjectBehavior
{
    use MessageFactoryBehavior;

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\MessageFactory\GuzzleMessageFactory');
    }
}
