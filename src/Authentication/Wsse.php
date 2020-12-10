<?php

namespace Http\Message\Authentication;

use Http\Message\Authentication;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;

/**
 * Authenticate a PSR-7 Request using WSSE.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class Wsse implements Authentication
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
     * @var string
     */
    private $hashAlgorithm;

    /**
     * @var array<string>
     */
    private $acceptedHashAlgos = ['sha1', 'sha512', 'sha384', 'sha3-384', 'sha3-512'];

    /**
     * @param string $username
     * @param string $password
     * @param string $hashAlgorithm To use a better hashing algorithm than the weak sha1, pass the algorithm to use, e.g. "sha512"
     */
    public function __construct($username, $password, $hashAlgorithm = 'sha1')
    {
        $this->username = $username;
        $this->password = $password;
        $this->hashAlgorithm = $hashAlgorithm;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(RequestInterface $request)
    {
        $nonce = substr(md5(uniqid(uniqid().'_', true)), 0, 16);
        $created = date('c');
        if (false === in_array($this->hashAlgorithm, $this->acceptedHashAlgos)) {
            throw new InvalidArgumentException(sprintf('Unaccepted hashing algorithm: %s', $this->hashAlgorithm));
        }
        $digest = base64_encode(hash($this->hashAlgorithm, base64_decode($nonce).$created.$this->password, true));

        $wsse = sprintf(
            'UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
            $this->username,
            $digest,
            $nonce,
            $created
        );

        return $request
            ->withHeader('Authorization', 'WSSE profile="UsernameToken"')
            ->withHeader('X-WSSE', $wsse)
        ;
    }
}
