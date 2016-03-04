<?php

namespace Http\Message\Authentication;

use Http\Message\Authentication;
use Http\Message\RequestMatcher\CallbackRequestMatcher;
use Psr\Http\Message\RequestInterface;

/**
 * Authenticate a PSR-7 Request if the request is matching.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @deprecated since since version 1.1, to be removed in 2.0. Use {@link RequestConditional} instead.
 */
final class Matching implements Authentication
{
    /**
     * @var Authentication
     */
    private $authentication;

    /**
     * @var CallbackRequestMatcher
     */
    private $matcher;

    /**
     * @param Authentication $authentication
     * @param callable|null  $matcher
     */
    public function __construct(Authentication $authentication, callable $matcher = null)
    {
        if (is_null($matcher)) {
            $matcher = function () {
                return true;
            };
        }

        $this->authentication = $authentication;
        $this->matcher = new CallbackRequestMatcher($matcher);
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(RequestInterface $request)
    {
        if ($this->matcher->matches($request)) {
            return $this->authentication->authenticate($request);
        }

        return $request;
    }

    /**
     * Creates a matching authentication for an URL.
     *
     * @param Authentication $authentication
     * @param string         $url
     *
     * @return self
     */
    public static function createUrlMatcher(Authentication $authentication, $url)
    {
        $matcher = function (RequestInterface $request) use ($url) {
            return preg_match($url, $request->getRequestTarget());
        };

        return new static($authentication, $matcher);
    }
}
