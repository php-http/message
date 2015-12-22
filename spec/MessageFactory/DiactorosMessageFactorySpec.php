<?php

namespace spec\Http\Message\MessageFactory;

use PhpSpec\ObjectBehavior;

class DiactorosMessageFactorySpec extends ObjectBehavior
{
    use MessageFactoryBehavior;

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\MessageFactory\DiactorosMessageFactory');
    }
}
