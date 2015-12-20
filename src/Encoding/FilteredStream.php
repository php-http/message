<?php

namespace Http\Message\Encoding;

use Clue\StreamFilter as Filter;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Http\Message\Decorator\StreamDecorator;
use Psr\Http\Message\StreamInterface;

/**
 * A filtered stream has a filter for filtering output and a filter for filtering input made to a underlying stream
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
abstract class FilteredStream implements StreamInterface
{
    const BUFFER_SIZE = 65536;

    use StreamDecorator;

    /**
     * @var callable
     */
    protected $readFilterCallback;

    /**
     * @var resource
     */
    protected $readFilter;

    /**
     * @var callable
     */
    protected $writeFilterCallback;

    /**
     * @var resource
     */
    protected $writeFilter;

    /**
     * Internal buffer
     *
     * @var string
     */
    protected $buffer = '';

    /**
     * @param StreamInterface $stream
     * @param null            $readFilterOptions
     * @param null            $writeFilterOptions
     */
    public function __construct(StreamInterface $stream, $readFilterOptions = null, $writeFilterOptions = null)
    {
        $this->readFilterCallback  = Filter\fun($this->getReadFilter(), $readFilterOptions);
        $this->writeFilterCallback = Filter\fun($this->getWriteFilter(), $writeFilterOptions);
        $this->stream              = $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        if (strlen($this->buffer) >= $length) {
            $read = substr($this->buffer, 0, $length);
            $this->buffer = substr($this->buffer, $length);

            return $read;
        }

        if ($this->stream->eof()) {
            $buffer = $this->buffer;
            $this->buffer = '';

            return $buffer;
        }

        $read = $this->buffer;
        $this->buffer = '';
        $this->fill();

        return $read . $this->read($length - strlen($read));
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return ($this->stream->eof() && $this->buffer === '');
    }

    /**
     * Buffer is filled by reading underlying stream
     *
     * Callback is reading once more even if the stream is ended.
     * This allow to get last data in the PHP buffer otherwise this
     * bug is present : https://bugs.php.net/bug.php?id=48725
     */
    protected function fill()
    {
        $readFilterCallback = $this->readFilterCallback;
        $this->buffer      .= $readFilterCallback($this->stream->read(self::BUFFER_SIZE));

        if ($this->stream->eof()) {
            $this->buffer .= $readFilterCallback();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        $buffer = '';

        while (!$this->eof()) {
            $buf = $this->read(1048576);
            // Using a loose equality here to match on '' and false.
            if ($buf == null) {
                break;
            }

            $buffer .= $buf;
        }

        return $buffer;
    }

    /**
     * Return the read filter name
     *
     * @return string
     */
    abstract public function getReadFilter();

    /**
     * Return the write filter name
     *
     * @return mixed
     */
    abstract public function getWriteFilter();
}
