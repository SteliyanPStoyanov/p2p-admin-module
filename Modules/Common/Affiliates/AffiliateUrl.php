<?php

namespace Modules\Common\Affiliates;

use Psr\Http\Message\UriInterface;

class AffiliateUrl implements UriInterface
{
    protected string $scheme = '';
    protected string $host = '';
    protected ?int $port = null;
    protected string $user = '';
    protected ?string $password = null;
    protected string $path = '';
    protected QueryParameter $query;
    protected string $fragment = '';
    public const VALID_SCHEMES = ['http', 'https', 'mailto'];

    /**
     * DoAffiliate constructor.
     */
    public function __construct()
    {
        $this->query = new QueryParameter();
    }

    public static function create()
    {
        return new static();
    }

    /**
     * @param string $url
     * @return static
     */
    public static function fromString(string $url): AffiliateUrl
    {
        $parts = array_merge(parse_url($url));

        $url = new static();
        $url->scheme = isset($parts['scheme']) ? $url->sanitizeScheme($parts['scheme']) : '';
        $url->host = $parts['host'] ?? '';
        $url->port = $parts['port'] ?? null;
        $url->user = $parts['user'] ?? '';
        $url->password = $parts['pass'] ?? null;
        $url->path = $parts['path'] ?? '/';
        $url->query = QueryParameter::fromString($parts['query'] ?? '');
        $url->fragment = $parts['fragment'] ?? '';

        return $url;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getAuthority(): string
    {
        $authority = $this->host;

        if ($this->getUserInfo()) {
            $authority = $this->getUserInfo().'@'.$authority;
        }

        if ($this->port !== null) {
            $authority .= ':'.$this->port;
        }

        return $authority;
    }

    /**
     * @return string
     */
    public function getUserInfo(): string
    {
        $userInfo = $this->user;

        if ($this->password !== null) {
            $userInfo .= ':'.$this->password;
        }

        return $userInfo;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getBasename(): string
    {
        return $this->getSegment(-1);
    }

    /**
     * @return string
     */
    public function getDirname(): string
    {
        $segments = $this->getSegments();

        array_pop($segments);

        return '/'.implode('/', $segments);
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return (string) $this->query;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getQueryParameter(string $key, $default = null)
    {
        return $this->query->get($key, $default);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasQueryParameter(string $key): bool
    {
        return $this->query->has($key);
    }

    /**
     * @return array
     */
    public function getAllQueryParameters(): array
    {
        return $this->query->all();
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function withQueryParameter(string $key, string $value): self
    {
        $url = clone $this;
        $url->query->unset($key);

        $url->query->set($key, $value);

        return $url;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function withoutQueryParameter(string $key): self
    {
        $url = clone $this;
        $url->query->unset($key);

        return $url;
    }

    /**
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @return array
     */
    public function getSegments(): array
    {
        return explode('/', trim($this->path, '/'));
    }

    /**
     * @param int $index
     * @param null $default
     * @return mixed|string|null
     */
    public function getSegment(int $index, $default = null)
    {
        $segments = $this->getSegments();

        if ($index === 0) {
           return '';
        }

        if ($index < 0) {
            $segments = array_reverse($segments);
            $index = abs($index);
        }

        return $segments[$index - 1] ?? $default;
    }

    /**
     * @return mixed|null
     */
    public function getFirstSegment()
    {
        $segments = $this->getSegments();

        return $segments[0] ?? null;
    }

    /**
     * @return false|mixed|null
     */
    public function getLastSegment()
    {
        $segments = $this->getSegments();

        return end($segments) ?? null;
    }

    /**
     * @param string $scheme
     * @return $this
     */
    public function withScheme($scheme): self
    {
        $url = clone $this;

        $url->scheme = $this->sanitizeScheme($scheme);

        return $url;
    }

    /**
     * @param string $scheme
     * @return string
     */
    protected function sanitizeScheme(string $scheme): string
    {
        $scheme = strtolower($scheme);

        if (! in_array($scheme, static::VALID_SCHEMES)) {
           return '';
        }

        return $scheme;
    }

    /**
     * @param string $user
     * @param null $password
     * @return $this
     */
    public function withUserInfo($user, $password = null): self
    {
        $url = clone $this;

        $url->user = $user;
        $url->password = $password;

        return $url;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function withHost($host): self
    {
        $url = clone $this;

        $url->host = $host;

        return $url;
    }

    /**
     * @param int|null $port
     * @return $this
     */
    public function withPort($port): self
    {
        $url = clone $this;

        $url->port = $port;

        return $url;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function withPath($path): self
    {
        $url = clone $this;

        if (! str_starts_with($path, '/')) {
            $path = '/'.$path;
        }

        $url->path = $path;

        return $url;
    }

    /**
     * @param string $dirname
     * @return $this
     */
    public function withDirname(string $dirname): self
    {
        $dirname = trim($dirname, '/');

        if (! $this->getBasename()) {
            return $this->withPath($dirname);
        }

        return $this->withPath($dirname.'/'.$this->getBasename());
    }

    /**
     * @param string $basename
     * @return $this
     */
    public function withBasename(string $basename): self
    {
        $basename = trim($basename, '/');

        if ($this->getDirname() === '/') {
            return $this->withPath('/'.$basename);
        }

        return $this->withPath($this->getDirname().'/'.$basename);
    }

    /**
     * @param string $query
     * @return $this
     */
    public function withQuery($query): self
    {
        $url = clone $this;

        $url->query = QueryParameter::fromString($query);

        return $url;
    }

    /**
     * @param string $fragment
     * @return $this
     */
    public function withFragment($fragment): self
    {
        $url = clone $this;

        $url->fragment = $fragment;

        return $url;
    }

    /**
     * @param AffiliateUrl $url
     * @return bool
     */
    public function matches(self $url): bool
    {
        return (string) $this === (string) $url;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $url = '';

        if ($this->getScheme() !== '' && $this->getScheme() !== 'mailto') {
            $url .= $this->getScheme().'://';
        }

        if ($this->getScheme() === 'mailto' && $this->getPath() !== '') {
            $url .= $this->getScheme().':';
        }

        if ($this->getScheme() === '' && $this->getAuthority() !== '') {
            $url .= '//';
        }

        if ($this->getAuthority() !== '') {
            $url .= $this->getAuthority();
        }

        if ($this->getPath() !== '/') {
            $url .= $this->getPath();
        }

        if ($this->getQuery() !== '') {
            $url .= '?'.$this->getQuery();
        }

        if ($this->getFragment() !== '') {
            $url .= '#'.$this->getFragment();
        }

        return $url;
    }

    /**
     *
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }
}
