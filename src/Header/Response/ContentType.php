<?php

namespace Hyqo\Http\Header\Response;

class ContentType implements ResponseHeaderInterface
{
    protected ?string $mediaType = null;

    protected ?string $charset = null;

    public function setMediaType(?string $mediaType = null): static
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    public function setCharset(?string $charset = null): static
    {
        $this->charset = $charset;

        return $this;
    }

    public function __toString(): string
    {
        if (null === $this->mediaType) {
            return '';
        }

        $string = $this->mediaType;

        if (null !== $this->charset) {
            $string .= sprintf('; charset=%s', $this->charset);
        }

        return $string;
    }
}
