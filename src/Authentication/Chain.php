<?php

namespace Http\Message\Authentication;

use Http\Message\Authentication;
use Psr\Http\Message\RequestInterface;

/**
 * Authenticate a PSR-7 Request with a multiple authentication methods.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class Chain implements Authentication
{
    /**
     * @var Authentication[]
     */
    private $authenticationChain = [];

    /**
     * @param Authentication[] $authenticationChain
     */
    public function __construct(array $authenticationChain = [])
    {
        $this->setAuthenticationChain($authenticationChain);
    }

    /**
     * Adds an Authentication method to the chain.
     *
     * The order of authentication methods SHOULD NOT matter.
     *
     * @param Authentication $authentication
     */
    public function addAuthentication(Authentication $authentication)
    {
        $this->authenticationChain[] = $authentication;
    }

    /**
     * Returns the current authentication chain.
     *
     * @return Authentication[]
     */
    public function getAuthenticationChain()
    {
        return $this->authenticationChain;
    }

    /**
     * Replaces the current authentication chain.
     *
     * @param array $authenticationChain
     */
    public function setAuthenticationChain(array $authenticationChain)
    {
        $this->clearAuthenticationChain();

        foreach ($authenticationChain as $authentication) {
            $this->addAuthentication($authentication);
        }
    }

    /**
     * Clears the authentication chain.
     */
    public function clearAuthenticationChain()
    {
        $this->authenticationChain = [];
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(RequestInterface $request)
    {
        foreach ($this->authenticationChain as $authentication) {
            $request = $authentication->authenticate($request);
        }

        return $request;
    }
}
