<?php

declare(strict_types=1);

namespace Http\Message\Tests\UriFactory;

use Http\Message\UriFactory;
use Http\Message\UriFactory\SlimUriFactory;
use Slim\Http\Uri;

final class SlimUriFactoryTest extends UriFactoryTestCase
{
    protected function setUp(): void
    {
        if (!class_exists(Uri::class)) {
            self::markTestSkipped('slim/slim 3 is not installed.');
        }
    }

    protected function createFactory(): UriFactory
    {
        return new SlimUriFactory();
    }
}
