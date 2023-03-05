<?php

namespace Hyqo\Http\Header\Request;

use function Hyqo\String\s;

/**
 * @internal
 */
abstract class AbstractEnumeratedHeader
{
    protected array $list = [];

    public function __construct(string $value = null)
    {
        $this->doSet($value);
    }

    protected function doSet(string $value = null): void
    {
        if (null === $value) {
            return;
        }

        $parts = s($value)->splitStrictly(',');

        if (!count($parts)) {
            return;
        }

        $list = [];
        foreach ($parts as $part) {
            if ($result = $this->handlePart($part)) {
                $list[] = $result;
            }
        }

        $this->list = $this->handleList($list);
    }

    /** @codeCoverageIgnore */
    protected function handlePart(string $part): ?array
    {
        return null;
    }

    protected function handleList(array $list): array
    {
        usort($list, static function (array $langA, array $langB) {
            if ($langA[1] === $langB[1]) {
                return 0;
            }
            return ($langA[1] < $langB[1]) ? 1 : -1;
        });

        $list = array_map(static function (array $lang) {
            return $lang[0];
        }, $list);

        return array_values(array_unique($list));
    }

    public function all(): array
    {
        return $this->list;
    }
}
