<?php

namespace Hyqo\Http;

use Hyqo\Http\Exception\HttpHeaderException;
use Hyqo\Http\Header\Request;

use function Hyqo\String\s;

/**
 * @property-read Request\Accept $accept
 * @property-read Request\AcceptEncoding $acceptEncoding
 * @property-read Request\AcceptLanguage $acceptLanguage
 * @property-read Request\CacheControl $cacheControl
 * @property-read ?int $contentLength
 * @property-read Request\ContentType $contentType
 * @property-read Request\Conditional $if
 * @property-read Request\Forwarded $forwarded
 * @property-read ?string $host
 * @property-read ?string $referer
 * @property-read ?string $userAgent
 */
class RequestHeaders
{
    protected array $headers = [];

    protected array $computedHeaders = [];

    protected array $headerHandlers;

    public function __construct(array $headers = [])
    {
        foreach ($headers as $name => $value) {
            $this->set($name, $value);
        }

        $this->headerHandlers = $this->getHeaderHandlers();
    }

    protected function getHeaderHandlers(): array
    {
        return [
            'accept' => fn() => new Header\Request\Accept($this->get('Accept')),
            'acceptEncoding' => fn() => new Header\Request\AcceptEncoding($this->get('Accept-Encoding')),
            'acceptLanguage' => fn() => new Header\Request\AcceptLanguage($this->get('Accept-Language')),

            'cacheControl' => fn() => new Header\Request\CacheControl($this->get('Cache-Control')),

            'contentLength' => fn() => (null === $value = $this->get('Content-Length')) ? null : (int)$value,
            'contentType' => fn() => new Header\Request\ContentType($this->get('Content-Type')),

            'if' => fn() => new Header\Request\Conditional([
                ...$this->pack('If-Match'),
                ...$this->pack('If-None-Match'),
                ...$this->pack('If-Modified-Since'),
                ...$this->pack('If-Range'),
                ...$this->pack('If-Unmodified-Since'),
            ]),

            'forwarded' => fn() => new Header\Request\Forwarded([
                ...$this->pack('X-Forwarded-Host'),
                ...$this->pack('X-Forwarded-For'),
                ...$this->pack('X-Forwarded-Port'),
                ...$this->pack('X-Forwarded-Prefix'),
                ...$this->pack('X-Forwarded-Proto'),
            ], $this->get('Forwarded')),

            'host' => fn() => $this->get('Host'),
            'referer' => fn() => $this->get('Referer'),
            'userAgent' => fn() => $this->get('User-Agent'),
        ];
    }

    public static function createFromGlobals(): self
    {
        return self::createFrom($_SERVER);
    }

    public static function createFrom(array $source): self
    {
        $headers = [];

        foreach ($source as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } elseif (\in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'], true)) {
                $headers[$key] = $value;
            }
        }

        return new self($headers);
    }

    protected function set(string $name, string $value): void
    {
        $name = (string)s($name)->pascalCase('-');

        $this->headers[$name] = $value;
    }

    protected function pack(string $key, ?string $default = null): array
    {
        return [$key => $this->get($key, $default)];
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return $this->has($key) ? $this->headers[$key] : $default;
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->headers);
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->headerHandlers);
    }

    public function __get($name)
    {
        if (isset($this->{$name})) {
            return $this->computedHeaders[$name] ??= $this->headerHandlers[$name]();
        }

        throw new HttpHeaderException("Property $name doesn't exist");
    }

    public function __set($name, $value)
    {
        throw new HttpHeaderException("Property $name cannot be set");
    }

    public function all(): array
    {
        return $this->headers;
    }
}
