<?php

namespace Hyqo\Http\Test\Header\Request;

use Hyqo\Http\Header\Request\AcceptEncoding;
use PHPUnit\Framework\TestCase;

class AcceptEncodingTest extends TestCase
{
    public function test_all(): void
    {
        $accept = new AcceptEncoding('*;q=0.8,gzip,compress,deflate,br,identity');

        $this->assertEquals(['gzip', 'compress', 'deflate', 'br', 'identity', '*'], $accept->all());
    }

    public function test_invalid(): void
    {
        $accept = new AcceptEncoding('foo');

        $this->assertEquals([], $accept->all());
    }
}
