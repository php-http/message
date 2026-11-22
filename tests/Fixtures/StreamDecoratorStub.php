<?php

declare(strict_types=1);

namespace Http\Message\Tests\Fixtures;

use Http\Message\Decorator\StreamDecorator;
use Psr\Http\Message\StreamInterface;

final class StreamDecoratorStub implements StreamInterface
{
    use StreamDecorator;

    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }
}
