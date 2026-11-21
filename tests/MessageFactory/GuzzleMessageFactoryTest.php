<?php

declare(strict_types=1);

namespace Http\Message\Tests\MessageFactory;

use Http\Message\MessageFactory;
use Http\Message\MessageFactory\GuzzleMessageFactory;

final class GuzzleMessageFactoryTest extends MessageFactoryTestCase
{
    protected function createFactory(): MessageFactory
    {
        return new GuzzleMessageFactory();
    }
}
