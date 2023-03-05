<?php

namespace Hyqo\Http\Header\Request;

use JetBrains\PhpStorm\ArrayShape;

use function Hyqo\Pair\parse_pair;
use function Hyqo\String\s;

readonly class ContentType
{
    public ?string $mediaType;
    public ?string $charset;
    public ?string $boundary;

    public function __construct(string $value = null)
    {
        [
            'mediaType' => $this->mediaType,
            'charset' => $this->charset,
            'boundary' => $this->boundary,
        ] = $this->doParse($value);
    }

    #[ArrayShape(['mediaType' => 'null|string', 'charset' => 'null|string', 'boundary' => 'null|string'])]
    protected function doParse(string $value = null): array
    {
        $result = [
            'mediaType' => null,
            'charset' => null,
            'boundary' => null,
        ];

        if (null === $value) {
            return $result;
        }

        $parts = s($value)->splitStrictly(';');

        if (
            count($parts) >= 1
            && preg_match(
                '/^(?P<type>text|application|image|audio|video|font|message|model|multipart)\/(?P<subtype>[\w\-.+]+)$/i',
                $parts[0],
                $matches
            )) {
            $mediaChunks = [strtolower($matches['type']), strtolower($matches['subtype'])];

            $result['mediaType'] = implode('/', $mediaChunks);

            foreach (array_slice($parts, 1) as $part) {
                if (null === $pair = parse_pair($part)) {
                    continue;
                }

                [$key, $value] = $pair;

                switch (strtolower($key)) {
                    case 'boundary':
                        if ($mediaChunks[0] === 'multipart' &&
                            preg_match("/^[\w\-\/'():=+?,. ]{0,69}[\w\-\/'():=+?,.]$/", $value)
                        ) {
                            $result['boundary'] = $value;
                        }
                        break;
                    case 'charset':
                        $result['charset'] = $value;
                        break;
                }
            }
        }

        return $result;
    }

    public function isText(): bool
    {
        return $this->mediaType === 'text/plain';
    }

    public function isHtml(): bool
    {
        return $this->mediaType === 'text/html';
    }

    public function isJson(): bool
    {
        return $this->mediaType === 'application/json';
    }

    public function isForm(): bool
    {
        return $this->mediaType === 'application/x-www-form-urlencoded';
    }

    public function isFormData(): bool
    {
        return $this->mediaType === 'multipart/form-data';
    }
}
