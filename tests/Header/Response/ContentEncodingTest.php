<?php

namespace Hyqo\Http\Test\Header\Response;

use Hyqo\Http\Header\Response\ContentEncoding;
use PHPUnit\Framework\TestCase;

class ContentEncodingTest extends TestCase
{
    public function test_encoding(): void
    {
        $contentEncoding = new ContentEncoding();
        $this->assertEquals('', (string)$contentEncoding);

        foreach (['gz', 'foo', 'bar'] as $encoding) {
            $contentEncoding->set($encoding);
            $this->assertEquals($encoding, (string)$contentEncoding);
        }
    }
}
