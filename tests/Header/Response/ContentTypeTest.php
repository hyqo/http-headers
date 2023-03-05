<?php

namespace Hyqo\Http\Test\Header\Response;

use Hyqo\Http\Header\Response\ContentType;
use PHPUnit\Framework\TestCase;

class ContentTypeTest extends TestCase
{
    public function test_media_type(): void
    {
        $contentType = (new ContentType)->setMediaType('application/json');

        $this->assertEquals('application/json', (string)$contentType);
    }

    public function test_media_type_and_charset(): void
    {
        $contentType = (new ContentType)->setMediaType('text/plain')->setCharset('utf-8');

        $this->assertEquals('text/plain; charset=utf-8', (string)$contentType);
    }

    public function test_empty_media_type(): void
    {
        $contentType = new ContentType;

        $this->assertEquals('', (string)$contentType);
    }
}
