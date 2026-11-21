<?php

declare(strict_types=1);

namespace Http\Message\Tests\UriFactory;

use Http\Message\UriFactory;
use Http\Message\UriFactory\DiactorosUriFactory;

final class DiactorosUriFactoryTest extends UriFactoryTestCase
{
    protected function createFactory(): UriFactory
    {
        return new DiactorosUriFactory();
    }
}
