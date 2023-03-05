<?php

namespace Hyqo\Http\Header;

use function Hyqo\Pair\parse_pair;
use function Hyqo\String\s;

/**
 * @internal
 */
trait CacheControlTrait
{
    public const MAX_AGE = 'max-age';

    public const NO_CACHE = 'no-cache';
    public const NO_STORE = 'no-store';
    public const NO_TRANSFORM = 'no-transform';

    protected array $directives = [];

    protected function doSet(string $value = null): static
    {
        if (null === $value) {
            return $this;
        }

        $parts = s($value)->splitStrictly(',');

        foreach ($parts as $part) {
            if (str_contains($part, '=')) {
                if ($pair = parse_pair($part)) {
                    [$directive, $value] = $pair;

                    $this->doSetDirective($directive, $value);
                }
            } else {
                $this->doSetDirective($part);
            }
        }

        return $this;
    }

    protected function doSetDirective(string $directive, ?int $value = null): static
    {
        $directive = strtolower($directive);

        if (null !== $value) {
            if (in_array($directive, static::WITH_VALUE, true)) {
                $this->directives[$directive] = $value;
            }
        } elseif (
            !in_array($directive, static::WITH_VALUE, true)
            && defined(sprintf('static::%s', s($directive)->snakeCase('_', true)))
        ) {
            $this->directives[$directive] = true;
        }

        return $this;
    }
}
