<?php

declare(strict_types=1);

namespace Http\Message\Tests\Decorator;

use Http\Message\Decorator\MessageDecorator;
use Http\Message\Tests\Fixtures\MessageDecoratorStub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

final class MessageDecoratorTest extends TestCase
{
    public function testUsesMessageDecoratorTrait(): void
    {
        self::assertContains(MessageDecorator::class, class_uses(MessageDecoratorStub::class));
    }

    public function testHasMessage(): void
    {
        $message = $this->createStub(MessageInterface::class);

        self::assertSame($message, (new MessageDecoratorStub($message))->getMessage());
    }

    public function testDelegatesGetProtocolVersion(): void
    {
        $message = $this->createStub(MessageInterface::class);
        $message->method('getProtocolVersion')->willReturn('1.1');

        self::assertSame('1.1', (new MessageDecoratorStub($message))->getProtocolVersion());
    }

    public function testDelegatesGetHeaders(): void
    {
        $headers = ['Content-Type' => ['application/xml']];
        $message = $this->createStub(MessageInterface::class);
        $message->method('getHeaders')->willReturn($headers);

        self::assertSame($headers, (new MessageDecoratorStub($message))->getHeaders());
    }

    public function testDelegatesHasHeader(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->expects($this->once())->method('hasHeader')->with('Content-Type')->willReturn(true);

        self::assertTrue((new MessageDecoratorStub($message))->hasHeader('Content-Type'));
    }

    public function testDelegatesGetHeader(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->expects($this->once())->method('getHeader')->with('Content-Type')->willReturn(['application/xml']);

        self::assertSame(['application/xml'], (new MessageDecoratorStub($message))->getHeader('Content-Type'));
    }

    public function testDelegatesGetHeaderLine(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->expects($this->once())->method('getHeaderLine')->with('Accept-Encoding')->willReturn('gzip, deflate');

        self::assertSame('gzip, deflate', (new MessageDecoratorStub($message))->getHeaderLine('Accept-Encoding'));
    }

    public function testDelegatesGetBody(): void
    {
        $body = $this->createStub(StreamInterface::class);
        $message = $this->createStub(MessageInterface::class);
        $message->method('getBody')->willReturn($body);

        self::assertSame($body, (new MessageDecoratorStub($message))->getBody());
    }

    public function testWithProtocolVersionReturnsNewDecorator(): void
    {
        $newMessage = $this->createStub(MessageInterface::class);
        $message = $this->createMock(MessageInterface::class);
        $message->expects($this->once())->method('withProtocolVersion')->with('1.1')->willReturn($newMessage);

        $this->assertImmutableDecoration($message, $newMessage, static function (MessageDecoratorStub $decorator): MessageInterface {
            return $decorator->withProtocolVersion('1.1');
        });
    }

    public function testWithHeaderReturnsNewDecorator(): void
    {
        $newMessage = $this->createStub(MessageInterface::class);
        $message = $this->createMock(MessageInterface::class);
        $message->expects($this->once())->method('withHeader')->with('Content-Type', 'application/xml')->willReturn($newMessage);

        $this->assertImmutableDecoration($message, $newMessage, static function (MessageDecoratorStub $decorator): MessageInterface {
            return $decorator->withHeader('Content-Type', 'application/xml');
        });
    }

    public function testWithAddedHeaderReturnsNewDecorator(): void
    {
        $newMessage = $this->createStub(MessageInterface::class);
        $message = $this->createMock(MessageInterface::class);
        $message->expects($this->once())->method('withAddedHeader')->with('Content-Type', 'application/xml')->willReturn($newMessage);

        $this->assertImmutableDecoration($message, $newMessage, static function (MessageDecoratorStub $decorator): MessageInterface {
            return $decorator->withAddedHeader('Content-Type', 'application/xml');
        });
    }

    public function testWithoutHeaderReturnsNewDecorator(): void
    {
        $newMessage = $this->createStub(MessageInterface::class);
        $message = $this->createMock(MessageInterface::class);
        $message->expects($this->once())->method('withoutHeader')->with('Content-Type')->willReturn($newMessage);

        $this->assertImmutableDecoration($message, $newMessage, static function (MessageDecoratorStub $decorator): MessageInterface {
            return $decorator->withoutHeader('Content-Type');
        });
    }

    public function testWithBodyReturnsNewDecorator(): void
    {
        $body = $this->createStub(StreamInterface::class);
        $newMessage = $this->createStub(MessageInterface::class);
        $message = $this->createMock(MessageInterface::class);
        $message->expects($this->once())->method('withBody')->with($body)->willReturn($newMessage);

        $this->assertImmutableDecoration($message, $newMessage, static function (MessageDecoratorStub $decorator) use ($body): MessageInterface {
            return $decorator->withBody($body);
        });
    }

    /**
     * @param callable(MessageDecoratorStub): MessageInterface $mutate
     */
    private function assertImmutableDecoration(MessageInterface $message, MessageInterface $newMessage, callable $mutate): void
    {
        $decorator = new MessageDecoratorStub($message);
        $new = $mutate($decorator);

        self::assertInstanceOf(MessageDecoratorStub::class, $new);
        self::assertNotSame($decorator, $new);
        self::assertSame($newMessage, $new->getMessage());
        self::assertSame($message, $decorator->getMessage());
    }
}
