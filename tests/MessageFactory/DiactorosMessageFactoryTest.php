<?php

declare(strict_types=1);

namespace Http\Message\Tests\MessageFactory;

use Http\Message\MessageFactory;
use Http\Message\MessageFactory\DiactorosMessageFactory;

final class DiactorosMessageFactoryTest extends MessageFactoryTestCase
{
    protected function createFactory(): MessageFactory
    {
        return new DiactorosMessageFactory();
    }
}
