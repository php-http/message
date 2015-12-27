<?php

namespace Http\Message\CookieJar;

use Http\Message\Cookie;

/**
 * Common implementation of a Cookie Jar.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait CookieJarTemplate
{
    /**
     * @var \SplObjectStorage
     */
    protected $cookies;

    /**
     * {@inheritdoc}
     */
    public function hasCookie(Cookie $cookie)
    {
        return $this->cookies->contains($cookie);
    }

    /**
     * {@inheritdoc}
     */
    public function addCookie(Cookie $cookie)
    {
        if (!$this->hasCookie($cookie)) {
            $cookies = $this->getMatchingCookies($cookie);

            foreach ($cookies as $matchingCookie) {
                if ($cookie->getValue() !== $matchingCookie->getValue() || $cookie->getExpires() > $matchingCookie->getExpires()) {
                    $this->removeCookie($matchingCookie);

                    continue;
                }
            }

            if ($cookie->hasValue()) {
                $this->cookies->attach($cookie);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeCookie(Cookie $cookie)
    {
        $this->cookies->detach($cookie);
    }

    /**
     * {@inheritdoc}
     */
    public function getCookies()
    {
        $match = function ($matchCookie) {
            return true;
        };

        return $this->findMatchingCookies($match);
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchingCookies(Cookie $cookie)
    {
        $match = function ($matchCookie) use ($cookie) {
            return $matchCookie->match($cookie);
        };

        return $this->findMatchingCookies($match);
    }

    /**
     * Finds matching cookies based on a callable.
     *
     * @param callable $match
     *
     * @return Cookie[]
     */
    protected function findMatchingCookies(callable $match)
    {
        $cookies = [];

        foreach ($this->cookies as $cookie) {
            if ($match($cookie)) {
                $cookies[] = $cookie;
            }
        }

        return $cookies;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCookies()
    {
        return $this->cookies->count() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function setCookies(array $cookies)
    {
        $this->clear();
        $this->addCookies($cookies);
    }

    /**
     * {@inheritdoc}
     */
    public function addCookies(array $cookies)
    {
        foreach ($cookies as $cookie) {
            $this->addCookie($cookie);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeCookies(array $cookies)
    {
        foreach ($cookies as $cookie) {
            $this->removeCookie($cookie);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeMatchingCookies($name = null, $domain = null, $path = null)
    {
        $match = function ($cookie) use ($name, $domain, $path) {
            $match = true;

            if (isset($name)) {
                $match = $match && ($cookie->getName() === $name);
            }

            if (isset($domain)) {
                $match = $match && $cookie->matchDomain($domain);
            }

            if (isset($path)) {
                $match = $match && $cookie->matchPath($path);
            }

            return $match;
        };

        $cookies = $this->findMatchingCookies($match);

        $this->removeCookies($cookies);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->cookies = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->cookies->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return clone $this->cookies;
    }
}
