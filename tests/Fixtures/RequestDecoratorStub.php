<?php

declare(strict_types=1);

namespace Http\Message\Tests\Fixtures;

use Http\Message\Decorator\RequestDecorator;
use Psr\Http\Message\RequestInterface;

final class RequestDecoratorStub implements RequestInterface
{
    use RequestDecorator;

    public function __construct(RequestInterface $request)
    {
        $this->message = $request;
    }
}
