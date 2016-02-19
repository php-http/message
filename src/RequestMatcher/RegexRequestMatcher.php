<?php

namespace Http\Message\RequestMatcher;

use Http\Message\RequestMatcher;
use Psr\Http\Message\RequestInterface;

/**
 * Match a request with a regex on the uri.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class RegexRequestMatcher implements RequestMatcher
{
    /**
     * Matching regex.
     *
     * @var string
     */
    private $regex;

    public function __construct($regex)
    {
        $this->regex = $regex;
    }

    /**
     * {@inheritdoc}
     */
    public function matches(RequestInterface $request)
    {
        return (bool) preg_match($this->regex, (string) $request->getUri());
    }
}
