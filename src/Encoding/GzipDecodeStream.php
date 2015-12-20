<?php

namespace Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;

/**
 * Stream for decoding from gzip format (RFC 1952)
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class GzipDecodeStream extends FilteredStream
{
    public function __construct(StreamInterface $stream, $level = -1)
    {
        parent::__construct($stream, ['window' => 31], ['window' => 31, 'level' => $level]);
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
