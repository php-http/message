<?php

namespace Http\Message\StreamFactory;

use Http\Message\StreamFactory;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream;

/**
 * Creates Diactoros streams.
 *
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 *
 * @deprecated This will be removed in php-http/message2.0. Consider using the official Diactoros PSR-17 factory
 */
final class DiactorosStreamFactory implements StreamFactory
{
    /**
     * {@inheritdoc}
     */
    public function createStream($body = null)
    {
        if ($body instanceof StreamInterface) {
            return $body;
        }

        if (is_resource($body)) {
            return new Stream($body);
        }

        $stream = new Stream('php://memory', 'rw');
        if (null !== $body && '' !== $body) {
            $stream->write((string) $body);
        }

        return $stream;
    }
}
