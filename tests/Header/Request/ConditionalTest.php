<?php

namespace Hyqo\Http\Test\Header\Request;

use Hyqo\Http\Header\ETag;
use Hyqo\Http\Header\Request\Conditional;
use PHPUnit\Framework\TestCase;

class ConditionalTest extends TestCase
{
    public function test_null_value(): void
    {
        $conditional = new Conditional(['If-Match' => null]);

        $this->assertNull($conditional->match);
        $this->assertNull($conditional->noneMatch);
        $this->assertNull($conditional->modifiedSince);
        $this->assertNull($conditional->unmodifiedSince);
    }

    public function test_invalid_values(): void
    {
        $conditional = new Conditional([
            'If-Match' => 'incorrect',
            'If-None-Match' => 'incorrect',
            'If-Modified-Since' => 'incorrect',
            'If-Unmodified-Since' => 'incorrect',
            'If-Range' => 'incorrect',
        ]);

        $this->assertNull($conditional->match);
        $this->assertNull($conditional->noneMatch);
        $this->assertNull($conditional->modifiedSince);
        $this->assertNull($conditional->unmodifiedSince);
    }

    public function test_match(): void
    {
        $conditional = new Conditional([
            'If-Match' => '"foo", W\"bar"',
            'If-None-Match' => '"foo2", W\"bar2"',
        ]);

        $this->assertArrayHasKey('foo', $conditional->match);
        $this->assertArrayHasKey('bar', $conditional->match);

        $this->assertArrayHasKey('foo2', $conditional->noneMatch);
        $this->assertArrayHasKey('bar2', $conditional->noneMatch);
    }

    public function test_modified(): void
    {
        $conditional = new Conditional([
            'If-Modified-Since' => 'Wed, 1 Oct 2015 07:28:00 GMT',
            'If-Unmodified-Since' => 'Wed, 1 Oct 2015 07:28:00 GMT',
        ]);

        $this->assertInstanceOf(\DateTimeImmutable::class, $conditional->modifiedSince);
        $this->assertInstanceOf(\DateTimeImmutable::class, $conditional->unmodifiedSince);
    }

    public function test_range_date(): void
    {
        $conditional = new Conditional([
            'If-Range' => 'Wed, 1 Oct 2015 07:28:00 GMT'
        ]);

        $this->assertInstanceOf(\DateTimeImmutable::class, $conditional->range);
    }

    public function test_range_etag(): void
    {
        $conditional = new Conditional([
            'If-Range' => '"foo"'
        ]);

        $this->assertInstanceOf(ETag::class, $conditional->range);
        $this->assertEquals('foo', $conditional->range->value);
    }

    public function test_range_weak_etag(): void
    {
        $conditional = new Conditional([
            'If-Range' => 'W\"bar","foo"'
        ]);

        $this->assertInstanceOf(ETag::class, $conditional->range);
        $this->assertEquals('bar', $conditional->range->value);
        $this->assertTrue($conditional->range->weak);
    }
}
