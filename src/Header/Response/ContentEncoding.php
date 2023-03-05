<?php

namespace Hyqo\Http\Header\Response;

use JetBrains\PhpStorm\ExpectedValues;

class ContentEncoding implements ResponseHeaderInterface
{
    protected ?string $value = null;

    public function __toString(): string
    {
        return $this->value ?: '';
    }

    public function set(#[ExpectedValues(valuesFromClass: ContentEncoding::class)] string $value = null): self
    {
        $this->value = $value;

        return $this;
    }
}
