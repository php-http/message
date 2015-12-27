<?php

namespace Http\Message;

/**
 * Cookie Value Object.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @see http://tools.ietf.org/search/rfc6265
 */
final class Cookie
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $value;

    /**
     * @var int|null
     */
    protected $maxAge;

    /**
     * @var \DateTime|null
     */
    protected $expires;

    /**
     * @var string|null
     */
    protected $domain;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var bool
     */
    protected $secure;

    /**
     * @var bool
     */
    protected $httpOnly;

    /**
     * @param string             $name
     * @param string|null        $value
     * @param int|\DateTime|null $expiration
     * @param string|null        $domain
     * @param string|null        $path
     * @param bool               $secure
     * @param bool               $httpOnly
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $name,
        $value = null,
        $expiration = 0,
        $domain = null,
        $path = null,
        $secure = false,
        $httpOnly = false
    ) {
        $this->validateName($name);
        $this->validateValue($value);

        $this->name = $name;
        $this->value = $value;
        $this->maxAge = is_int($expiration) ? $expiration : null;
        $this->expires = $this->normalizeExpires($expiration);
        $this->domain = $this->normalizeDomain($domain);
        $this->path = $this->normalizePath($path);
        $this->secure = (bool) $secure;
        $this->httpOnly = (bool) $httpOnly;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value.
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Checks if there is a value.
     *
     * @return bool
     */
    public function hasValue()
    {
        return isset($this->value);
    }

    /**
     * Sets the value.
     *
     * @param string|null $value
     *
     * @return self
     */
    public function withValue($value)
    {
        $this->validateValue($value);

        $new = clone $this;
        $new->value = $value;

        return $new;
    }

    /**
     * Returns the max age.
     *
     * @return int|null
     */
    public function getMaxAge()
    {
        return $this->maxAge;
    }

    /**
     * Checks if there is a max age.
     *
     * @return bool
     */
    public function hasMaxAge()
    {
        return isset($this->maxAge);
    }

    /**
     * Sets the max age.
     *
     * @param int|null $maxAge
     *
     * @return self
     */
    public function withMaxAge($maxAge)
    {
        $new = clone $this;
        $new->maxAge = is_int($maxAge) ? $maxAge : null;

        return $new;
    }

    /**
     * Sets both the max age and the expires attributes.
     *
     * @param int|\DateTime|null $expiration
     *
     * @return self
     */
    public function withExpiration($expiration)
    {
        $new = clone $this;
        $new->maxAge = is_int($expiration) ? $expiration : null;
        $new->expires = $this->normalizeExpires($expiration);

        return $new;
    }

    /**
     * Returns the expiration time.
     *
     * @return \DateTime|null
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Checks if there is an expiration time.
     *
     * @return bool
     */
    public function hasExpires()
    {
        return isset($this->expires);
    }

    /**
     * Sets the expires.
     *
     * @param \DateTime|null $expires
     *
     * @return self
     */
    public function withExpires(\DateTime $expires)
    {
        $new = clone $this;
        $new->expires = $expires;

        return $new;
    }

    /**
     * Checks if the cookie is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return isset($this->expires) and $this->expires < new \DateTime();
    }

    /**
     * Returns the domain.
     *
     * @return string|null
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Checks if there is a domain.
     *
     * @return bool
     */
    public function hasDomain()
    {
        return isset($this->domain);
    }

    /**
     * Sets the domain.
     *
     * @param string|null $domain
     *
     * @return self
     */
    public function withDomain($domain)
    {
        $new = clone $this;
        $new->domain = $this->normalizeDomain($domain);

        return $new;
    }

    /**
     * Matches a domain.
     *
     * @param string $domain
     *
     * @return bool
     *
     * @see http://tools.ietf.org/html/rfc6265#section-5.1.3
     */
    public function matchDomain($domain)
    {
        // Domain is not set or exact match
        if (!$this->hasDomain() || strcasecmp($domain, $this->domain) === 0) {
            return true;
        }

        // Domain is not an IP address
        if (filter_var($domain, FILTER_VALIDATE_IP)) {
            return false;
        }

        return (bool) preg_match('/\b'.preg_quote($this->domain).'$/i', $domain);
    }

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the path.
     *
     * @param string|null $path
     *
     * @return self
     */
    public function withPath($path)
    {
        $new = clone $this;
        $new->path = $this->normalizePath($path);

        return $new;
    }

    /**
     * It matches a path.
     *
     * @param string $path
     *
     * @return bool
     *
     * @see http://tools.ietf.org/html/rfc6265#section-5.1.4
     */
    public function matchPath($path)
    {
        return $this->path === $path || (strpos($path, $this->path.'/') === 0);
    }

    /**
     * Checks if HTTPS is required.
     *
     * @return bool
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * Sets the secure.
     *
     * @param bool $secure
     *
     * @return self
     */
    public function withSecure($secure)
    {
        $new = clone $this;
        $new->secure = (bool) $secure;

        return $new;
    }

    /**
     * Checks if it is HTTP-only.
     *
     * @return bool
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * Sets the HTTP Only.
     *
     * @param bool $httpOnly
     *
     * @return self
     */
    public function withHttpOnly($httpOnly)
    {
        $new = clone $this;
        $new->httpOnly = (bool) $httpOnly;

        return $new;
    }

    /**
     * Checks if it matches with another cookie.
     *
     * @param Cookie $cookie
     *
     * @return bool
     */
    public function match(Cookie $cookie)
    {
        return $this->name === $cookie->name && $this->domain === $cookie->domain and $this->path === $cookie->path;
    }

    /**
     * Validates the name attribute.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @see http://tools.ietf.org/search/rfc2616#section-2.2
     */
    private function validateName($name)
    {
        if (strlen($name) < 1) {
            throw new \InvalidArgumentException('The name cannot be empty');
        }

        // Name attribute is a token as per spec in RFC 2616
        if (preg_match('/[\x00-\x20\x22\x28-\x29\x2c\x2f\x3a-\x40\x5b-\x5d\x7b\x7d\x7f]/', $name)) {
            throw new \InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
        }
    }

    /**
     * Validates a value.
     *
     * @param string|null $value
     *
     * @throws \InvalidArgumentException
     *
     * @see http://tools.ietf.org/html/rfc6265#section-4.1.1
     */
    private function validateValue($value)
    {
        if (isset($value)) {
            if (preg_match('/[^\x21\x23-\x2B\x2D-\x3A\x3C-\x5B\x5D-\x7E]/', $value)) {
                throw new \InvalidArgumentException(sprintf('The cookie value "%s" contains invalid characters.', $value));
            }
        }
    }

    /**
     * Normalizes the expiration value.
     *
     * @param int|\DateTime|null $expiration
     *
     * @return \DateTime|null
     */
    private function normalizeExpires($expiration)
    {
        $expires = null;

        if (is_int($expiration)) {
            $expires = new \DateTime(sprintf('%d seconds', $expiration));

            // According to RFC 2616 date should be set to earliest representable date
            if ($expiration <= 0) {
                $expires->setTimestamp(-PHP_INT_MAX);
            }
        } elseif ($expiration instanceof \DateTime) {
            $expires = $expiration;
        }

        return $expires;
    }

    /**
     * Remove the leading '.' and lowercase the domain as per spec in RFC 6265.
     *
     * @param string|null $domain
     *
     * @return string
     *
     * @see http://tools.ietf.org/html/rfc6265#section-4.1.2.3
     * @see http://tools.ietf.org/html/rfc6265#section-5.1.3
     * @see http://tools.ietf.org/html/rfc6265#section-5.2.3
     */
    private function normalizeDomain($domain)
    {
        if (isset($domain)) {
            $domain = ltrim(strtolower($domain), '.');
        }

        return $domain;
    }

    /**
     * Processes path as per spec in RFC 6265.
     *
     * @param string|null $path
     *
     * @return string
     *
     * @see http://tools.ietf.org/html/rfc6265#section-5.1.4
     * @see http://tools.ietf.org/html/rfc6265#section-5.2.4
     */
    private function normalizePath($path)
    {
        $path = rtrim($path, '/');

        if (empty($path) or substr($path, 0, 1) !== '/') {
            $path = '/';
        }

        return $path;
    }
}
