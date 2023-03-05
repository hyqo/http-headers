<?php

namespace Hyqo\Http\Test\Header\Request;

use Hyqo\Http\Header\Request\Accept;
use PHPUnit\Framework\TestCase;

class AcceptTest extends TestCase
{
    public function test_all(): void
    {
        $accept = new Accept('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');

        $this->assertEquals(['text/html', 'application/xhtml+xml', 'application/xml', '*/*'], $accept->all());
    }

    public function test_invalid(): void
    {
        $accept = new Accept('foo');

        $this->assertEquals([], $accept->all());
    }
}
