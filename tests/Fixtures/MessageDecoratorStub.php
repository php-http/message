<?php

declare(strict_types=1);

namespace Http\Message\Tests\Fixtures;

use Http\Message\Decorator\MessageDecorator;
use Psr\Http\Message\MessageInterface;

final class MessageDecoratorStub implements MessageInterface
{
    use MessageDecorator;

    public function __construct(MessageInterface $message)
    {
        $this->message = $message;
    }
}
