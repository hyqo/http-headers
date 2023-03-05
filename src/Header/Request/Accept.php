<?php

namespace Hyqo\Http\Header\Request;

class Accept extends AbstractEnumeratedHeader
{
    protected function handlePart(string $part): ?array
    {
        if (preg_match(
            '/^(?P<mediaType>[\w+*]+\/[\w+*]+)(?:;q=(?P<quality>0.\d+))?$/i',
            $part,
            $matches
        )) {
            $mediaType = $matches['mediaType'];
            $quality = (float)($matches['quality'] ?? 1.0);

            return [$mediaType, $quality];
        }

        return null;
    }
}
