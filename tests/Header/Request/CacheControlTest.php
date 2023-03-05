<?php

namespace Hyqo\Http\Test\Header\Request;

use Hyqo\Http\Header\Request\CacheControl;
use PHPUnit\Framework\TestCase;

class CacheControlTest extends TestCase
{
    public function test_directive(): void
    {
        $cacheControl = new CacheControl(
            implode(', ', [
                'max-age=123',
                'min-fresh=456',
                'max-stale=789',
                'no-cache',
                'no-store',
                'no-transform',
                'only-if-cached',
                'foo',
            ])
        );

        $this->assertTrue($cacheControl->hasMaxAge());
        $this->assertEquals(123, $cacheControl->getMaxAge());

        $this->assertTrue($cacheControl->hasMinFresh());
        $this->assertEquals(456, $cacheControl->getMinFresh());

        $this->assertTrue($cacheControl->hasMaxStale());
        $this->assertEquals(789, $cacheControl->getMaxStale());

        $this->assertTrue($cacheControl->hasNoCache());
        $this->assertTrue($cacheControl->hasNoStore());
        $this->assertTrue($cacheControl->hasNoTransform());
        $this->assertTrue($cacheControl->hasOnlyIfCached());

        $this->assertFalse($cacheControl->has('foo'));
        $this->assertFalse($cacheControl->has('bar'));

        $this->assertEquals([
            'max-age' => 123,
            'min-fresh' => 456,
            'max-stale' => 789,
            'no-cache' => true,
            'no-store' => true,
            'no-transform' => true,
            'only-if-cached' => true,
        ], $cacheControl->all());
    }

    public function test_null(): void
    {
        $cacheControl = new CacheControl();

        $this->assertEquals([], $cacheControl->all());
    }
}
