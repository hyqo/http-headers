<?php

namespace Hyqo\Http\Header\Request;

class AcceptLanguage extends AbstractEnumeratedHeader
{
    protected function handlePart(string $part): ?array
    {
        if (preg_match(
            '/^(?:(?P<language>[a-z]{2})(?:-(?P<variety>[a-z]+))?|(?P<all>\*))(?:;q=(?P<quality>0.\d+))?$/i',
            $part,
            $matches
        )) {
            $language = ($matches['all'] ?? '') ?: strtolower($matches['language']);
//                $variety = strtolower($matches['variety'] ?? '');
            $quality = (float)($matches['quality'] ?? 1.0);

            return [$language, $quality];
        }

        return null;
    }
}
