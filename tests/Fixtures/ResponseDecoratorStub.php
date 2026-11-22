<?php

declare(strict_types=1);

namespace Http\Message\Tests\Fixtures;

use Http\Message\Decorator\ResponseDecorator;
use Psr\Http\Message\ResponseInterface;

final class ResponseDecoratorStub implements ResponseInterface
{
    use ResponseDecorator;

    public function __construct(ResponseInterface $response)
    {
        $this->message = $response;
    }
}
