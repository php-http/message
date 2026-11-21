<?php

declare(strict_types=1);

namespace Http\Message\Tests\StreamFactory;

use Http\Message\StreamFactory;
use Http\Message\StreamFactory\SlimStreamFactory;
use Psr\Http\Message\StreamInterface;
use Slim\Http\Stream;

final class SlimStreamFactoryTest extends StreamFactoryTestCase
{
    protected function setUp(): void
    {
        if (!class_exists(Stream::class)) {
            self::markTestSkipped('slim/slim 3 is not installed.');
        }
    }

    protected function createFactory(): StreamFactory
    {
        return new SlimStreamFactory();
    }

    protected function createNativeStream($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
