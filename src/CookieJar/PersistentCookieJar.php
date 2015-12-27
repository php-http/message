<?php

namespace Http\Message\CookieJar;

use Http\Message\CookieJar;

/**
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
interface PersistentCookieJar extends CookieJar
{
    /**
     * Loads the cookie jar.
     *
     * @throws \RuntimeException
     */
    public function load();

    /**
     * Saves the cookie jar.
     *
     * @throws \RuntimeException
     */
    public function save();
}
