<?php

namespace Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;

/**
 * Stream compress (RFC 1950).
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class CompressStream extends FilteredStream
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

        parent::__construct($stream, ['window' => 15, 'level' => $level], ['window' => 15]);
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
