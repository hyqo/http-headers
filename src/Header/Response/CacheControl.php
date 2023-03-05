<?php

namespace Hyqo\Http\Header\Response;

use Hyqo\Http\Header;

class CacheControl implements ResponseHeaderInterface
{
    use Header\CacheControlTrait;

    public const S_MAXAGE = 's-maxage';

    public const MUST_REVALIDATE = 'must-revalidate';
    public const PROXY_REVALIDATE = 'proxy-revalidate';
    public const MUST_UNDERSTAND = 'must-understand';

    public const PRIVATE = 'private';
    public const PUBLIC = 'public';

    public const IMMUTABLE = 'immutable';
    public const STALE_WHILE_REVALIDATE = 'stale-while-revalidate';
    public const STALE_IF_ERROR = 'stale-if-error';

    protected const WITH_VALUE = [
        self::MAX_AGE,
        self::S_MAXAGE,
        self::STALE_WHILE_REVALIDATE,
        self::STALE_IF_ERROR,
    ];

    public function __toString(): string
    {
        if (!$this->directives) {
            return '';
        }

        return implode(
            ', ',
            array_map(static function (string $directive, $value) {
                if ($value === true) {
                    return $directive;
                }

                return sprintf('%s=%s', $directive, $value);
            }, array_keys($this->directives), array_values($this->directives))
        );
    }

    public function setNoCache(): static
    {
        return $this->setDirective(self::NO_CACHE);
    }

    public function setNoStore(): static
    {
        return $this->setDirective(self::NO_STORE);
    }

    public function setNoTransform(): static
    {
        return $this->setDirective(self::NO_TRANSFORM);
    }

    public function setPublic(): static
    {
        return $this
            ->deleteDirective(self::PRIVATE)
            ->setDirective(self::PUBLIC);
    }

    public function setPrivate(): static
    {
        return $this
            ->deleteDirective(self::PUBLIC)
            ->setDirective(self::PRIVATE);
    }

    public function setMaxAge(int $value): static
    {
        return $this->setDirective(self::MAX_AGE, $value);
    }

    public function setSMaxAge(int $value): static
    {
        return $this->setDirective(self::S_MAXAGE, $value);
    }

    public function setMustRevalidate(): static
    {
        return $this->setDirective(self::MUST_REVALIDATE);
    }

    public function setProxyRevalidate(): static
    {
        return $this->setDirective(self::PROXY_REVALIDATE);
    }

    public function setMustUnderstand(): static
    {
        return $this->setDirective(self::MUST_UNDERSTAND);
    }

    public function setImmutable(): static
    {
        return $this->setDirective(self::IMMUTABLE);
    }

    public function setStaleWhileRevalidate(int $value): static
    {
        return $this->setDirective(self::STALE_WHILE_REVALIDATE, $value);
    }

    public function setStaleIfError(int $value): static
    {
        return $this->setDirective(self::STALE_IF_ERROR, $value);
    }

    public function set(string $value): static
    {
        return $this->doSet($value);
    }

    public function setDirective(string $directive, ?int $value = null): static
    {
        return $this->doSetDirective($directive, $value);
    }

    public function deleteDirective(string $directive): static
    {
        if (array_key_exists($directive, $this->directives)) {
            unset($this->directives[$directive]);
        }

        return $this;
    }

    public function clear(): static
    {
        $this->directives = [];

        return $this;
    }
}
