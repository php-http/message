<?php

declare(strict_types=1);

namespace Http\Message\Tests\Decorator;

use Http\Message\Decorator\ResponseDecorator;
use Http\Message\Tests\Fixtures\ResponseDecoratorStub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class ResponseDecoratorTest extends TestCase
{
    public function testUsesResponseDecoratorTrait(): void
    {
        self::assertContains(ResponseDecorator::class, class_uses(ResponseDecoratorStub::class));
    }

    public function testHasResponse(): void
    {
        $response = $this->createStub(ResponseInterface::class);

        self::assertSame($response, (new ResponseDecoratorStub($response))->getResponse());
    }

    public function testWithResponseReturnsNewDecorator(): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $otherResponse = $this->createStub(ResponseInterface::class);
        $decorator = new ResponseDecoratorStub($response);

        $new = $decorator->withResponse($otherResponse);

        self::assertInstanceOf(ResponseDecoratorStub::class, $new);
        self::assertSame($otherResponse, $new->getResponse());
        self::assertSame($response, $decorator->getResponse());
    }

    public function testDelegatesGetStatusCode(): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        self::assertSame(200, (new ResponseDecoratorStub($response))->getStatusCode());
    }

    public function testDelegatesGetReasonPhrase(): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getReasonPhrase')->willReturn('OK');

        self::assertSame('OK', (new ResponseDecoratorStub($response))->getReasonPhrase());
    }

    public function testWithStatusReturnsNewDecorator(): void
    {
        $newResponse = $this->createStub(ResponseInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('withStatus')->with(200, 'OK')->willReturn($newResponse);

        $new = (new ResponseDecoratorStub($response))->withStatus(200, 'OK');

        self::assertInstanceOf(ResponseDecoratorStub::class, $new);
        self::assertSame($newResponse, $new->getMessage());
    }
}
