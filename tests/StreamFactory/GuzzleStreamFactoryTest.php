<?php

declare(strict_types=1);

namespace Http\Message\Tests\StreamFactory;

use GuzzleHttp\Psr7\Stream;
use Http\Message\StreamFactory;
use Http\Message\StreamFactory\GuzzleStreamFactory;
use Psr\Http\Message\StreamInterface;

final class GuzzleStreamFactoryTest extends StreamFactoryTestCase
{
    protected function createFactory(): StreamFactory
    {
        return new GuzzleStreamFactory();
    }

    protected function createNativeStream($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
