<?php

declare(strict_types=1);

namespace Http\Message\Tests\MessageFactory;

use Http\Message\MessageFactory;
use Http\Message\MessageFactory\SlimMessageFactory;
use Slim\Http\Request;

final class SlimMessageFactoryTest extends MessageFactoryTestCase
{
    protected function setUp(): void
    {
        if (!class_exists(Request::class)) {
            self::markTestSkipped('slim/slim 3 is not installed.');
        }
    }

    protected function createFactory(): MessageFactory
    {
        return new SlimMessageFactory();
    }
}
