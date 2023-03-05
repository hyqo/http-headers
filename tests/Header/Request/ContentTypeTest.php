<?php

namespace Hyqo\Http\Test\Header\Request;

use Hyqo\Http\Header\Request\ContentType;
use PHPUnit\Framework\TestCase;

class ContentTypeTest extends TestCase
{
    public function test_media_type(): void
    {
        $contentType = new ContentType('application/json');

        $this->assertEquals('application/json', $contentType->mediaType);
        $this->assertNull($contentType->charset);
        $this->assertNull($contentType->boundary);
    }

    public function test_media_type_and_charset(): void
    {
        $contentType = new ContentType('text/plain; charset=utf-8');

        $this->assertEquals('text/plain', $contentType->mediaType);
        $this->assertEquals('utf-8', $contentType->charset);
        $this->assertNull($contentType->boundary);
    }

    public function test_boundary(): void
    {
        $contentType = new ContentType('multipart/form-data; boundary=------------foo');

        $this->assertEquals('multipart/form-data', $contentType->mediaType);
        $this->assertNull($contentType->charset);
        $this->assertEquals('------------foo', $contentType->boundary);
    }

    public function test_invalid_media_type_with_boundary(): void
    {
        $contentType = new ContentType('application/json; boundary=------------foo');

        $this->assertEquals('application/json', $contentType->mediaType);
        $this->assertNull($contentType->charset);
        $this->assertNull($contentType->boundary);
    }

    public function test_empty_boundary(): void
    {
        $contentType = new ContentType('multipart/form-data; boundary=');

        $this->assertEquals('multipart/form-data', $contentType->mediaType);
        $this->assertNull($contentType->charset);
        $this->assertNull($contentType->boundary);
    }

    public function test_invalid_boundary(): void
    {
        $contentType = new ContentType('multipart/form-data; boundary=foo bar');

        $this->assertEquals('multipart/form-data', $contentType->mediaType);
        $this->assertNull($contentType->charset);
        $this->assertNull($contentType->boundary);
    }

    public function test_null(): void
    {
        $contentType = new ContentType();

        $this->assertNull($contentType->mediaType);
        $this->assertNull($contentType->charset);
        $this->assertNull($contentType->boundary);
    }

    public function test_is_text(): void
    {
        $contentType = new ContentType('text/plain');

        $this->assertTrue($contentType->isText());
    }

    public function test_is_html(): void
    {
        $contentType = new ContentType('text/html');

        $this->assertTrue($contentType->isHtml());
    }

    public function test_is_form(): void
    {
        $contentType = new ContentType('application/x-www-form-urlencoded');

        $this->assertTrue($contentType->isForm());
    }

    public function test_is_json(): void
    {
        $contentType = new ContentType('application/json');

        $this->assertTrue($contentType->isJson());
    }

    public function test_is_form_data(): void
    {
        $contentType = new ContentType('multipart/form-data');

        $this->assertTrue($contentType->isFormData());
    }
}
