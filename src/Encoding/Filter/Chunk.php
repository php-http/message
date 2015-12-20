<?php

namespace Http\Message\Encoding\Filter;

class Chunk extends \php_user_filter
{
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $lenbucket = stream_bucket_new($this->stream, dechex($bucket->datalen)."\r\n");
            stream_bucket_append($out, $lenbucket);

            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);

            $lenbucket = stream_bucket_new($this->stream, "\r\n");
            stream_bucket_append($out, $lenbucket);
        }

        return PSFS_PASS_ON;
    }
}
