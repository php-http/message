<?php

namespace spec\Http\Message\StreamFactory;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;

trait StreamFactoryBehavior
{
    function it_is_a_stream_factory()
    {
        $this->shouldImplement('Http\Message\StreamFactory');
    }

    function it_creates_a_stream_from_string()
    {
        $this->createStream('foo')->shouldHaveType('Psr\Http\Message\StreamInterface');
    }

    function it_creates_a_stream_from_resource()
    {
        $this->createStream(fopen('php://memory', 'rw'))
            ->shouldHaveType('Psr\Http\Message\StreamInterface');
    }

    function it_creates_a_stream_from_null()
    {
        $this->createStream(null)->shouldHaveType('Psr\Http\Message\StreamInterface');
    }

    function it_creates_a_stream_from_non_seekable_resource()
    {
        $url = 'https://raw.githubusercontent.com/php-http/multipart-stream-builder/master/tests/Resources/httplug.png';
        $resource = fopen($url, 'r');
        $this->createStream($resource)
            ->shouldHaveType('Psr\Http\Message\StreamInterface');
    }

    function it_does_not_rewind_existing_stream()
    {
        $stream = new Stream(fopen('php://memory', 'rw'));
        $stream->write('abcdef');
        $stream->seek(3);

        $this->createStream($stream)
            ->shouldHaveContent('def');
    }

    function it_does_not_rewind_existing_resource()
    {
        $resource = fopen('php://memory', 'rw');
        fwrite($resource, 'abcdef');
        fseek($resource, 3);

        $this->createStream($resource)
            ->shouldHaveContent('def');
    }

    public function getMatchers()
    {
        return [
            'haveContent' => function (StreamInterface $subject, $key) {
                return $subject->getContents() === $key;
            },
        ];
    }
}
