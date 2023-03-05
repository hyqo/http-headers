<?php

namespace Hyqo\Http\Header\Request;

use DateTimeImmutable;
use DateTimeInterface;
use Hyqo\Http\Header\ETag;

readonly class Conditional
{
    /** @var ETag[]|null */
    public ?array $match;

    /** @var ETag[]|null */
    public ?array $noneMatch;

    public ?DateTimeImmutable $modifiedSince;

    public ?DateTimeImmutable $unmodifiedSince;

    public null|ETag|DateTimeImmutable $range;

    public function __construct(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->set($name, $value);
        }

        $initialized = array_keys(get_object_vars($this));

        foreach (['match', 'noneMatch', 'modifiedSince', 'unmodifiedSince', 'range'] as $property) {
            if (!in_array($property, $initialized, true)) {
                $this->{$property} = null;
            }
        }
    }

    protected function set(string $name, ?string $value): void
    {
        if (null === $value) {
            return;
        }

        switch ($name) {
            case 'If-Match':
                $this->match = $this->parseETags($value);
                return;
            case 'If-None-Match':
                $this->noneMatch = $this->parseETags($value);
                return;
            case 'If-Modified-Since':
                $this->modifiedSince = $this->parseDateTime($value);
                return;
            case 'If-Unmodified-Since':
                $this->unmodifiedSince = $this->parseDateTime($value);
                return;
            case 'If-Range':
                $this->range = $this->parseDateTime($value) ?? (($eTags = $this->parseETags($value)) ? current(
                    $eTags
                ) : null);
                return;
        }
    }

    protected function parseDateTime(string $value): ?DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat(DateTimeInterface::RFC7231, $value) ?: null;
    }

    /** @return ETag[]|null */
    protected function parseETags(string $value): ?array
    {
        $tags = preg_split('/(?<=")\s*,\s*(?=(W\\\\)?")/', $value);
        $result = [];

        foreach ($tags as $tag) {
            $tag = trim($tag);

            if (preg_match(
                '/^(?P<weak>W\\\\)?"(?P<value>(?:[\x20\x21\x23-\x5b\x5d-\x7e]|\r\n[\t ]|\\\\"|\\\\[^"])*)"$/',
                $tag,
                $matches
            )) {
                $result[$matches['value']] = new ETag($matches['value'], (bool)$matches['weak']);
            }
        }

        return $result ?: null;
    }
}
