<?php

namespace Hyqo\Http\Test\Header\Response;

use Hyqo\Http\Header\Response\CacheControl;
use PHPUnit\Framework\TestCase;

class CacheControlTest extends TestCase
{
    protected function createCacheControl(): CacheControl
    {
        return (new CacheControl())
            ->setPublic()
            ->setPrivate()
            ->setNoCache()
            ->setNoStore()
            ->setNoTransform()
            ->setMaxAge(123)
            ->setSMaxAge(123)
            ->setMustRevalidate()
            ->setProxyRevalidate()
            ->setMustUnderstand()
            ->setImmutable()
            ->setStaleWhileRevalidate(123)
            ->setStaleIfError(123);
    }

    public function test_set_directive(): void
    {
        $expected = implode(', ', [
            'private',
            'no-cache',
            'no-store',
            'no-transform',
            'max-age=123',
            's-maxage=123',
            'must-revalidate',
            'proxy-revalidate',
            'must-understand',
            'immutable',
            'stale-while-revalidate=123',
            'stale-if-error=123'
        ]);

        $cacheControl = $this->createCacheControl();

        $this->assertEquals($expected, (string)$cacheControl);
    }

    public function test_bulk_set_directive(): void
    {
        $expected = implode(', ', [
            'private',
            'no-cache',
            'no-store',
            'no-transform',
            'max-age=123',
            's-maxage=123',
            'must-revalidate',
            'proxy-revalidate',
            'must-understand',
            'immutable',
            'stale-while-revalidate=123',
            'stale-if-error=123'
        ]);

        $cacheControl = (new CacheControl())->set($expected);

        $this->assertEquals($expected, (string)$cacheControl);
    }

    public function test_clear_directives(): void
    {
        $cacheControl = $this->createCacheControl()->clear();

        $this->assertEquals('', (string)$cacheControl);
    }
}
