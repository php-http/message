<?php

namespace Http\Message\Encoding;

/**
 * Transform a regular stream into a chunked one.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class ChunkStream extends FilteredStream
{
    /**
     * {@inheritdoc}
     */
    public function getReadFilter()
    {
        return 'chunk';
    }

    /**
     * {@inheritdoc}
     */
    public function getWriteFilter()
    {
        return 'dechunk';
    }

    /**
     * {@inheritdoc}
     */
    protected function fill()
    {
        parent::fill();

        if ($this->stream->eof()) {
            $this->buffer .= "0\r\n\r\n";
        }
    }
}
