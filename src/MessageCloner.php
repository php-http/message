<?php

namespace Http\Message;

use Psr\Http\Message\MessageInterface;

/**
 * Allow to clone request and response.
 *
 * A message cloner is only necessary when you also want to duplicate the stream of a request or a response, as by
 * default object returned by `clone $message` call will have the same stream as the cloned one, so reading the body of
 * one of the message will affect the other.
 */
class MessageCloner
{
    /**
     * Clone a message.
     *
     * When cloning you have to be careful that the original stream is rewindable. If not the original stream will be
     * readed and cannot be readed one more time. To avoid this behavior, when the original stream is not seekable, you
     * can clone the cloned message, and replace the original message with one of the clone, as there are always rewindable.
     *
     * @param MessageInterface $message
     *
     * @return MessageInterface|static
     */
    public function cloneMessage(MessageInterface $message)
    {
        return $message->withBody(new MemoryClonedStream($message->getBody()));
    }
}
