<?php

namespace Hyqo\Http\Header\Request;

class AcceptEncoding extends AbstractEnumeratedHeader
{
    protected function handlePart(string $part): ?array
    {
        if (preg_match(
            '/^(?P<encoding>gzip|compress|deflate|br|identity|\*)(?:;q=(?P<quality>0.\d+))?$/i',
            $part,
            $matches
        )) {
            $encoding = $matches['encoding'];
            $quality = (float)($matches['quality'] ?? 1.0);

            return [$encoding, $quality];
        }

        return null;
    }
}
