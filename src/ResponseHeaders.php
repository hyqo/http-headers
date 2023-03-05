<?php

namespace Hyqo\Http;

use Hyqo\Http\Exception\HttpHeaderException;
use Hyqo\Http\Header\ETag;
use Hyqo\Http\Header\Response;

/**
 * @property Header\Response\CacheControl $cacheControl
 * @property Header\Response\ContentType $contentType
 * @property Header\Response\ContentEncoding $contentEncoding
 * @property Header\Response\ContentDisposition $contentDisposition
 */
class ResponseHeaders
{
    protected ?HttpCode $code = null;

    /** @var array<string,string> */
    protected array $headers = [];

    /** @var array<string, Response\ResponseHeaderInterface> */
    protected array $richHeaders = [];

    protected const RICH_MAP = [
        'cacheControl' => [
            'Cache-Control',
            Header\Response\CacheControl::class
        ],
        'contentType' => [
            'Content-Type',
            Header\Response\ContentType::class
        ],
        'contentDisposition' => [
            'Content-Disposition',
            Header\Response\ContentDisposition::class
        ],
        'contentEncoding' => [
            'Content-Encoding',
            Header\Response\ContentEncoding::class
        ],
    ];

    /** @return array<int,string> */
    public function all(): array
    {
        $result = [];

        if ($this->code) {
            $result[] = $this->code->header();
        }

        foreach ($this->each() as $name => $value) {
            $result[] = sprintf("%s: %s", $name, $value);
        }

        return $result;
    }

    public function each(): \Generator
    {
        foreach ($this->richHeaders as $name => $header) {
            if ($value = (string)$header) {
                yield self::RICH_MAP[$name][0] => $value;
            }
        }

        foreach ($this->headers as $name => $value) {
            yield $name => $value;
        }
    }

    public function __get($name)
    {
        if (!array_key_exists($name, self::RICH_MAP)) {
            throw new HttpHeaderException("Property $name doesn't exist");
        }

        return $this->richHeaders[$name] ??= new (self::RICH_MAP[$name][1]);
    }

    public function __set($name, $value)
    {
        throw new HttpHeaderException("Property $name cannot be set");
    }

    public function __isset($name)
    {
        return array_key_exists($name, self::RICH_MAP);
    }

    public function set(string $name, string $value): static
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function setCode(HttpCode $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function setETag(string $value, bool $weak = false): static
    {
        $this->set('ETag', new ETag($value, $weak));

        return $this;
    }

    public function setContentType(string $mediaType, ?string $charset = null): static
    {
        $this->contentType
            ->setMediaType($mediaType)
            ->setCharset($charset);

        return $this;
    }

    public function setCacheControl(callable $callback): static
    {
        $callback($this->cacheControl);

        return $this;
    }

    public function setContentDisposition(callable $callback): static
    {
        $callback($this->contentDisposition);

        return $this;
    }

    public function setContentDispositionInline(): static
    {
        $this->contentDisposition->setInline();

        return $this;
    }

    public function setContentDispositionAttachment(?string $filename = null): static
    {
        $this->contentDisposition->setAttachment($filename);

        return $this;
    }

    public function setContentEncoding(string $encoding): static
    {
        $this->contentEncoding->set($encoding);

        return $this;
    }

    public function setContentLength(int $length): static
    {
        $this->set('Content-Length', $length);

        return $this;
    }

}
