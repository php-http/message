<?php

namespace spec\Http\Message\UriFactory;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\UriInterface;

class GuzzleUriFactorySpec extends ObjectBehavior
{
    use UriFactoryBehavior;

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\UriFactory\GuzzleUriFactory');
    }

    /**
     * TODO: Remove this when https://github.com/phpspec/phpspec/issues/825 is resolved.
     */
    public function it_creates_a_uri_from_uri(UriInterface $uri)
    {
        $this->createUri($uri)->shouldReturn($uri);
    }
}
