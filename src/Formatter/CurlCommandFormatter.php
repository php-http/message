<?php

namespace Http\Message\Formatter;

use Http\Message\Formatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A formatter that prints a cURL command for HTTP requests.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class CurlCommandFormatter implements Formatter
{
    /**
     * {@inheritdoc}
     */
    public function formatRequest(RequestInterface $request)
    {
        $command = sprintf('curl %s', escapeshellarg($request->getUri()));
        if ($request->getProtocolVersion() === '1.0') {
            $command .= ' --http1.0';
        } elseif ($request->getProtocolVersion() === '2.0') {
            $command .= ' --http2';
        }

        $command .= ' --request '.$request->getMethod();

        foreach ($request->getHeaders() as $name => $values) {
            $command .= sprintf(' -H %s', escapeshellarg($name.': '.$request->getHeaderLine($name)));
        }

        $body = $request->getBody();
        if ($body->getSize() > 0) {
            if (!$body->isSeekable()) {
                throw new \RuntimeException('Cannot take data from a stream that is not seekable');
            }
            $command .= sprintf(' --data %s', escapeshellarg($body->__toString()));
            $body->rewind();
        }

        return $command;
    }

    /**
     * {@inheritdoc}
     */
    public function formatResponse(ResponseInterface $response)
    {
        return '';
    }
}
