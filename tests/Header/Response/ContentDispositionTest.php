<?php

namespace Hyqo\Http\Test\Header\Response;

use Hyqo\Http\Header\Response\ContentDisposition;
use PHPUnit\Framework\TestCase;

class ContentDispositionTest extends TestCase
{
    public function test_empty(): void
    {
        $contentDisposition = new ContentDisposition();

        $this->assertEquals('', (string)$contentDisposition);
    }

    public function test_inline(): void
    {
        $contentDisposition = new ContentDisposition();
        $contentDisposition->setInline();

        $this->assertEquals('inline', (string)$contentDisposition);
    }

    public function test_attachment(): void
    {
        $contentDisposition = new ContentDisposition();
        $contentDisposition->setAttachment();

        $this->assertEquals('attachment', (string)$contentDisposition);
    }

    public function test_attachment_with_filename(): void
    {
        $contentDisposition = new ContentDisposition();
        $contentDisposition->setAttachment('foo.jpg');

        $this->assertEquals('attachment; filename="foo.jpg"', (string)$contentDisposition);
    }
}
