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
    public function __construct(StreamInterface $stream, $level = -1)
    {
        parent::__construct($stream, ['window' => 15, 'level' => $level], ['window' => 15]);
    }

    public function getReadFilter()
    {
        return 'zlib.deflate';
    }

    public function getWriteFilter()
    {
        return 'zlib.inflate';
    }
}
