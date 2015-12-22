<?php

namespace spec\Http\Message\UriFactory;

use Psr\Http\Message\UriInterface;
use PhpSpec\ObjectBehavior;

class GuzzleUriFactorySpec extends ObjectBehavior
{
    use UriFactoryBehavior;

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\UriFactory\GuzzleUriFactory');
    }

    /**
     * TODO: Remove this when https://github.com/phpspec/phpspec/issues/825 is resolved
     */
    function it_creates_a_uri_from_uri(UriInterface $uri)
    {
        $this->createUri($uri)->shouldReturn($uri);
    }
}
