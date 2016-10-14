<?php

namespace Http\Message\StreamFactory;

use Http\Message\StreamFactory;
use Psr\Http\Message\StreamInterface;
use Slim\Http\Stream;

/**
 * Creates Slim 3 streams.
 *
 * @author Mika Tuupola <tuupola@appelsiini.net>
 */
final class SlimStreamFactory implements StreamFactory
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
            $stream = new Stream($body);
        } else {
            $resource = fopen('php://memory', 'r+');
            $stream = new Stream($resource);

            if (null !== $body) {
                $stream->write((string) $body);
            }
        }

        $stream->rewind();

        return $stream;
    }
}
