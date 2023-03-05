<?php

namespace Hyqo\Http\Header\Request;

use JetBrains\PhpStorm\ArrayShape;

use function Hyqo\Pair\parse_pair;
use function Hyqo\String\s;

readonly class Forwarded
{
    public ?array $for;
    public ?string $proto;
    public ?string $host;
    public ?int $port;
    public ?string $prefix;

    public function __construct(array $x_forwarded_headers = [], ?string $forwarded_header = null)
    {
        $data = [
            'for' => null,
            'proto' => null,
            'host' => null,
            'port' => null,
            'prefix' => null,
        ];

        if ($forwarded_header) {
            $data = [...$data, ...$this->set($forwarded_header)];
        } else {
            foreach ($x_forwarded_headers as $name => $value) {
                $data = [...$data, ...$this->setX($name, $value)];
            }
        }

        [
            'for' => $this->for,
            'proto' => $this->proto,
            'host' => $this->host,
            'port' => $this->port,
            'prefix' => $this->prefix,
        ] = $data;
    }

    #[ArrayShape(['for' => '?array', 'proto' => '?string', 'host' => '?string'])]
    protected function set(string $value): array
    {
        $for = [];
        $proto = null;
        $host = null;

        $parts = s($value)->splitStrictly(';');

        foreach ($parts as $part) {
            $items = s($part)->splitStrictly(',');

            foreach ($items as $item) {
                if (!$pair = parse_pair($item)) {
                    continue;
                }

                [$key, $value] = $pair;

                switch (strtolower($key)) {
                    case 'for':
                    case 'by':
                        $for[] = $value;
                        break;
                    case 'proto':
                        $proto = $this->parseValue(strtolower($value), ['https', 'http']);
                        break;
                    case 'host':
                        $host = $value;
                        break;
                }
            }
        }

        return [
            'for' => $for ?: null,
            'proto' => $proto,
            'host' => $host,
        ];
    }

    #[ArrayShape([
        'for' => 'array',
        'proto' => 'string',
        'host' => 'string',
        'port' => 'string',
        'prefix' => 'string',
    ])]
    protected function setX(string $name, ?string $value): array
    {
        if (null === $value) {
            return [];
        }

        return match ($name) {
            'X-Forwarded-For' => ['for' => $this->parseList($value)],
            'X-Forwarded-Host' => ['host' => $this->parseValue($value)],
            'X-Forwarded-Port' => ['port' => (int)$value],
            'X-Forwarded-Prefix' => ['prefix' => $value],
            'X-Forwarded-Proto' => ['proto' => $this->parseValue(strtolower($value), ['https', 'http'])],
            default => [],
        };
    }

    protected function parseList(string $string): array
    {
        $result = [];

        $values = s($string)->splitStrictly(',');

        foreach ($values as $value) {
            $result[] = $this->parseValue($value);
        }

        return $result;
    }

    protected function parseValue(string $value, ?array $allow = null): ?string
    {
        $value = trim($value, '"');

        if (null === $allow) {
            return $value;
        }

        if (in_array($value, $allow, true)) {
            return $value;
        }

        return null;
    }
}
