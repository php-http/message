<?php

declare(strict_types=1);

namespace Http\Message\Tests\StreamFactory;

use Http\Message\StreamFactory;
use Http\Message\StreamFactory\DiactorosStreamFactory;
use Laminas\Diactoros\Stream;
use Psr\Http\Message\StreamInterface;

final class DiactorosStreamFactoryTest extends StreamFactoryTestCase
{
    protected function createFactory(): StreamFactory
    {
        return new DiactorosStreamFactory();
    }

    protected function createNativeStream($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
