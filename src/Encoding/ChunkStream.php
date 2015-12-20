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
        if (!array_key_exists('chunk', stream_get_filters())) {
            stream_filter_register('chunk', 'Http\Message\Encoding\Filter\Chunk');
        }

        return 'chunk';
    }

    /**
     * {@inheritdoc}
     */
    public function getWriteFilter()
    {
        return 'dechunk';
    }

    protected function fill()
    {
        parent::fill();

        if ($this->stream->eof()) {
            $this->buffer .= "0\r\n\r\n";
        }
    }
}
