<?php

namespace Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;

/**
 * Stream deflate (RFC 1951)
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class DeflateStream extends FilteredStream
{
    public function __construct(StreamInterface $stream, $level = -1)
    {
        parent::__construct($stream, ['window' => -15, 'level' => $level], ['window' => -15]);
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
