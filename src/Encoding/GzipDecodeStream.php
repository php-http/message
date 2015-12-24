<?php

namespace Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;

/**
 * Stream for decoding from gzip format (RFC 1952).
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class GzipDecodeStream extends FilteredStream
{
    /**
     * @param StreamInterface $stream
     * @param int             $level
     */
    public function __construct(StreamInterface $stream, $level = -1)
    {
        if (!extension_loaded('zlib')) {
            throw new \RuntimeException('The zlib extension must be enabled to use this stream');
        }

        parent::__construct($stream, ['window' => 31], ['window' => 31, 'level' => $level]);
    }

    /**
     * {@inheritdoc}
     */
    public function getReadFilter()
    {
        return 'zlib.inflate';
    }

    /**
     * {@inheritdoc}
     */
    public function getWriteFilter()
    {
        return 'zlib.deflate';
    }
}
