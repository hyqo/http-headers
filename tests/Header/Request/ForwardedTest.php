<?php

namespace Hyqo\Http\Test\Header\Request;

use Hyqo\Http\Header\Request\Forwarded;
use PHPUnit\Framework\TestCase;

class ForwardedTest extends TestCase
{
    public function test_null(): void
    {
        $forwarder = new Forwarded();

        $this->assertNull($forwarder->for);
        $this->assertNull($forwarder->proto);
        $this->assertNull($forwarder->host);
        $this->assertNull($forwarder->port);
        $this->assertNull($forwarder->prefix);
    }

    public function test_x_null(): void
    {
        $forwarder = new Forwarded([
            'X-Forwarded-Proto' => null,
        ]);

        $this->assertNull($forwarder->proto);
    }

    public function test_invalid_x_proto(): void
    {
        $forwarder = new Forwarded([
            'X-Forwarded-Proto' => 'foo',
        ]);

        $this->assertNull($forwarder->proto);
    }

    public function test_x_forwarder(): void
    {
        $forwarder = new Forwarded([
            'X-Forwarded-For' => '127.0.0.1, [::0]',
            'X-Forwarded-Host' => 'google.com',
            'X-Forwarded-Port' => '80',
            'X-Forwarded-Prefix' => '/',
            'X-Forwarded-Proto' => 'http',
        ]);

        $this->assertEquals(['127.0.0.1', '[::0]'], $forwarder->for);
        $this->assertEquals('http', $forwarder->proto);
        $this->assertEquals('google.com', $forwarder->host);
        $this->assertEquals(80, $forwarder->port);
        $this->assertEquals('/', $forwarder->prefix);
    }

    public function test_invalid_forwarder(): void
    {
        $forwarder = new Forwarded([],
            implode(';', [
                'for="127.0.0.1',
            ]));

        $this->assertNull($forwarder->for);
    }

    public function test_forwarder(): void
    {
        $forwarder = new Forwarded([],
            implode(';', [
                'for=192.0.2.60,for="[2001:db8:cafe::17]"',
                'host=google.com',
                'proto=https',
                'by=203.0.113.43'
            ]));

        $this->assertEquals(['192.0.2.60', '[2001:db8:cafe::17]', '203.0.113.43'], $forwarder->for);
        $this->assertEquals('https', $forwarder->proto);
        $this->assertEquals('google.com', $forwarder->host);
        $this->assertNull($forwarder->port);
        $this->assertNull($forwarder->prefix);
    }
}
