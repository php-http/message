<?php

namespace Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;

/**
 * Stream for encoding to gzip format (RFC 1952).
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class GzipEncodeStream extends FilteredStream
{
    public function __construct(StreamInterface $stream, $level = -1)
    {
        parent::__construct($stream, ['window' => 31, 'level' => $level], ['window' => 31]);
    }

    /**
     * {@inheritdoc}
     */
    public function getReadFilter()
    {
        return 'zlib.deflate';
    }

    /**
     * {@inheritdoc}
     */
    public function getWriteFilter()
    {
        return 'zlib.inflate';
    }
}
