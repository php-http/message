<?php

namespace Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;

/**
 * Stream decompress (RFC 1950).
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class DecompressStream extends FilteredStream
{
    public function __construct(StreamInterface $stream, $level = -1)
    {
        parent::__construct($stream, ['window' => 15], ['window' => 15, 'level' => $level]);
    }

    public function getReadFilter()
    {
        return 'zlib.inflate';
    }

    public function getWriteFilter()
    {
        return 'zlib.deflate';
    }
}
