<?php

namespace Hyqo\Http\Header;

class ETag
{
    public string $value;
    public bool $weak;

    public function __construct(string $value, bool $weak = false)
    {
        $this->value = $value;
        $this->weak = $weak;
    }

    public function __toString()
    {
        return sprintf('%s"%s"', $this->weak ? 'W/' : '', addcslashes($this->value, "\x0..\x1f\x22\x5c\x7e"));
    }
}
