<?php

namespace Hyqo\Http\Test\Header;

use Hyqo\Http\Header\ETag;
use PHPUnit\Framework\TestCase;

class ETagTest extends TestCase
{
    public function test_create_strong_etag(): void
    {
        $etag = new ETag('foo');

        $this->assertEquals('"foo"', (string)$etag);
    }

    public function test_create_weak_etag(): void
    {
        $etag = new ETag('bar', true);

        $this->assertEquals('W/"bar"', (string)$etag);
    }
}
