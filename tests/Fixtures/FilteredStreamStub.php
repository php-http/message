<?php

declare(strict_types=1);

namespace Http\Message\Tests\Fixtures;

use Http\Message\Encoding\FilteredStream;

final class FilteredStreamStub extends FilteredStream
{
    protected function readFilter(): string
    {
        return 'convert.base64-encode';
    }

    protected function writeFilter(): string
    {
        return 'convert.base64-encode';
    }
}
