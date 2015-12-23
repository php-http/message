<?php

namespace Http\Message\Authentication;

/**
 * Trait implementing the common username-password pattern.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait UserPasswordPair
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * Returns the username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
