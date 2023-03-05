<?php

namespace Hyqo\Http\Test;

use Hyqo\Http\Exception\HttpHeaderException;
use Hyqo\Http\Header\Response\CacheControl;
use Hyqo\Http\Header\Response\ContentDisposition;
use Hyqo\Http\HttpCode;
use Hyqo\Http\ResponseHeaders;
use PHPUnit\Framework\TestCase;

class ResponseHeadersTest extends TestCase
{
    protected function describeRichHeaders(): \Generator
    {
        $reflectionClass = new \ReflectionClass(ResponseHeaders::class);
        $constant = $reflectionClass->getConstant('RICH_MAP');

        foreach ($constant as $property => $value) {
            yield [$property, $value[1]];
        }
    }

    /**
     * @dataProvider describeRichHeaders
     */
    public function test_rich_header_class(string $name, string $class): void
    {
        $this->assertInstanceOf($class, (new ResponseHeaders())->{$name});
    }

    /**
     * @dataProvider describeRichHeaders
     */
    public function test_rich_header_exists(string $name): void
    {
        $this->assertTrue(isset((new ResponseHeaders())->{$name}));
    }

    public function test_rich_header_does_not_exist(): void
    {
        $this->assertFalse(isset((new ResponseHeaders())->foo));
    }

    public function test_invalid_rich_header(): void
    {
        $this->expectException(HttpHeaderException::class);
        $this->expectExceptionMessage("Property foo doesn't exist");

        /** @noinspection PhpUndefinedFieldInspection */
        (new ResponseHeaders())->foo;
    }

    public function test_magic_set(): void
    {
        $this->expectException(HttpHeaderException::class);
        $this->expectExceptionMessage("Property foo cannot be set");

        $response = new ResponseHeaders();
        /** @noinspection PhpUndefinedFieldInspection */
        $response->foo = 'bar';
    }

    public function test_empty_headers(): void
    {
        $responseHeaders = new ResponseHeaders();

        $this->assertEquals([], iterator_to_array($responseHeaders->each()));
    }

    public function test_headers(): void
    {
        $responseHeaders = (new ResponseHeaders())
            ->setCode(HttpCode::OK)
            ->setContentType('text/plain')
            ->setContentEncoding('gz')
            ->setContentLength(123)
            ->setCacheControl(fn(CacheControl $cacheControl) => $cacheControl->setNoCache()->setMaxAge(123))
            ->set('foo', 'bar')
            ->setETag('foo');

        $this->assertEquals([
            'HTTP/1.0 200 OK',
            'Content-Type: text/plain',
            'Content-Encoding: gz',
            'Cache-Control: no-cache, max-age=123',
            'Content-Length: 123',
            'foo: bar',
            'ETag: "foo"',
        ], $responseHeaders->all());

        $reflection = new \ReflectionClass(ResponseHeaders::class);
        $reflectionMethod = $reflection->getProperty('code');
        /** @noinspection PhpExpressionResultUnusedInspection */
        $reflectionMethod->setAccessible(true);

        $code = $reflectionMethod->getValue($responseHeaders);

        $this->assertEquals(HttpCode::OK, $code);
    }

    public function test_set_disposition_inline_via_direct(): void
    {
        $responseHeaders = new ResponseHeaders();
        $responseHeaders->contentDisposition->setInline();

        $this->assertEquals([
            'Content-Disposition: inline',
        ], $responseHeaders->all());
    }

    public function test_set_disposition_inline_via_callback(): void
    {
        $responseHeaders = new ResponseHeaders();
        $responseHeaders->setContentDisposition(
            fn(ContentDisposition $contentDisposition) => $contentDisposition->setInline()
        );

        $this->assertEquals([
            'Content-Disposition: inline',
        ], $responseHeaders->all());
    }

    public function test_set_disposition_inline_via_method(): void
    {
        $responseHeaders = new ResponseHeaders();
        $responseHeaders->setContentDispositionInline();

        $this->assertEquals([
            'Content-Disposition: inline',
        ], $responseHeaders->all());
    }

    public function test_set_disposition_attachment_via_direct(): void
    {
        $responseHeaders = new ResponseHeaders();
        $responseHeaders->contentDisposition->setAttachment();

        $this->assertEquals([
            'Content-Disposition: attachment',
        ], $responseHeaders->all());


        $responseHeaders->contentDisposition->setAttachment('foo.jpeg');

        $this->assertEquals([
            'Content-Disposition: attachment; filename="foo.jpeg"',
        ], $responseHeaders->all());
    }

    public function test_set_disposition_attachment_via_callback(): void
    {
        $responseHeaders = new ResponseHeaders();
        $responseHeaders->setContentDisposition(
            fn(ContentDisposition $contentDisposition) => $contentDisposition->setAttachment()
        );

        $this->assertEquals([
            'Content-Disposition: attachment',
        ], $responseHeaders->all());


        $responseHeaders->setContentDisposition(
            fn(ContentDisposition $contentDisposition) => $contentDisposition->setAttachment('foo.jpeg')
        );

        $this->assertEquals([
            'Content-Disposition: attachment; filename="foo.jpeg"',
        ], $responseHeaders->all());
    }

    public function test_set_disposition_attachment_via_method(): void
    {
        $responseHeaders = new ResponseHeaders();
        $responseHeaders->setContentDispositionAttachment();

        $this->assertEquals([
            'Content-Disposition: attachment',
        ], $responseHeaders->all());


        $responseHeaders->setContentDispositionAttachment('foo.jpeg');

        $this->assertEquals([
            'Content-Disposition: attachment; filename="foo.jpeg"',
        ], $responseHeaders->all());
    }
}
