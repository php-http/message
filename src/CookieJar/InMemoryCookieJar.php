<?php

namespace Http\Message\CookieJar;

use Http\Message\CookieJar;

/**
 * Common implementation of a Cookie Jar.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class InMemoryCookieJar implements CookieJar
{
    use CookieJarTemplate;

    public function __construct()
    {
        $this->cookies = new \SplObjectStorage();
    }
}
