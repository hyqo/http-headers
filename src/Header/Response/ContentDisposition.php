<?php

namespace Hyqo\Http\Header\Response;

use function Hyqo\Pair\build_pair;

class ContentDisposition implements ResponseHeaderInterface
{
    public const INLINE = 'inline';
    public const ATTACHMENT = 'attachment';

    protected ?string $value = null;

    public function __toString(): string
    {
        return $this->value ?: '';
    }

    public function setInline(): void
    {
        $this->value = self::INLINE;
    }

    public function setAttachment(?string $filename = null): void
    {
        $this->value = self::ATTACHMENT;

        if ($filename) {
            $this->value .= '; ' . build_pair('filename', $filename);
        }
    }
}
