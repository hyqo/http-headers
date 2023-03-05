<?php

namespace Hyqo\Http\Header\Request;

use Hyqo\Http\Header;

class CacheControl
{
    use Header\CacheControlTrait;

    public const MAX_STALE = 'max-stale';
    public const MIN_FRESH = 'min-fresh';

    public const ONLY_IF_CACHED = 'only-if-cached';

    protected const WITH_VALUE = [
        self::MAX_AGE,
        self::MAX_STALE,
        self::MIN_FRESH,
    ];

    public function __construct(string $value = null)
    {
        $this->doSet($value);
    }

    public function has(string $directive): bool
    {
        return array_key_exists(strtolower($directive), $this->directives);
    }

    public function get(string $directive): bool|int|null
    {
        return $this->directives[strtolower($directive)] ?? null;
    }

    public function all(): array
    {
        return $this->directives;
    }

    public function hasNoStore(): bool
    {
        return $this->has(self::NO_STORE);
    }

    public function hasNoCache(): bool
    {
        return $this->has(self::NO_CACHE);
    }

    public function hasNoTransform(): bool
    {
        return $this->has(self::NO_TRANSFORM);
    }

    public function hasMaxAge(): bool
    {
        return $this->has(self::MAX_AGE);
    }

    public function getMaxAge(): bool|int|null
    {
        return $this->get(self::MAX_AGE);
    }

    public function hasMaxStale(): bool
    {
        return $this->has(self::MAX_STALE);
    }

    public function getMaxStale(): bool|int|null
    {
        return $this->get(self::MAX_STALE);
    }

    public function hasMinFresh(): bool
    {
        return $this->has(self::MIN_FRESH);
    }

    public function getMinFresh(): bool|int|null
    {
        return $this->get(self::MIN_FRESH);
    }

    public function hasOnlyIfCached(): bool
    {
        return $this->has(self::ONLY_IF_CACHED);
    }
}
