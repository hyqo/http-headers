<?php

namespace Hyqo\Http\Header;

use Generator;

interface HeaderInterface
{
    /**
     * @return Generator<string,void,void,string>
     */
    public function generator(): Generator;
}
