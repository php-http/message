<?php

namespace Http\Message\Encoding;

/**
 * Decorate a stream which is chunked.
 *
 * Allow to decode a chunked stream
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class DechunkStream extends FilteredStream
{
    /**
     * {@inheritdoc}
     */
    public function getReadFilter()
    {
        return 'dechunk';
    }

    /**
     * {@inheritdoc}
     */
    public function getWriteFilter()
    {
        if (!array_key_exists('chunk', stream_get_filters())) {
            stream_filter_register('chunk', 'Http\Encoding\Filter\Chunk');
        }

        return 'chunk';
    }
}
