<?php

namespace Http\Message\Authentication;

use Http\Message\Authentication;
use Psr\Http\Message\RequestInterface;

/**
 * Authenticate a PSR-7 Request if the reuqest is matching.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class Matching implements Authentication
{
    /**
     * @var Authentication
     */
    private $authentication;

    /**
     * @var callable
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
        $this->matcher = $matcher;
    }

    /**
     * Returns the authentication.
     *
     * @return string
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * Sets the authentication.
     *
     * @param Authentication $authentication
     */
    public function setAuthentication(Authentication $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * Returns the matcher.
     *
     * @return callable
     */
    public function getMatcher()
    {
        return $this->matcher;
    }

    /**
     * Sets the matcher.
     *
     * @param callable $matcher
     */
    public function setMatcher(callable $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(RequestInterface $request)
    {
        if (!call_user_func($this->matcher, $request)) {
            return $request;
        }

        return $this->authentication->authenticate($request);
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
        $matcher = function ($request) use ($url) {
            return preg_match($url, $request->getRequestTarget());
        };

        return new static($authentication, $matcher);
    }
}
