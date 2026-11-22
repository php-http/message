<?php

declare(strict_types=1);

namespace Http\Message\Tests\UriFactory;

use Http\Message\UriFactory;
use Http\Message\UriFactory\GuzzleUriFactory;

final class GuzzleUriFactoryTest extends UriFactoryTestCase
{
    protected function createFactory(): UriFactory
    {
        return new GuzzleUriFactory();
    }
}
