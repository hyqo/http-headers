<?php

namespace Hyqo\Http\Test\Header\Request;

use Hyqo\Http\Header\Request\AbstractEnumeratedHeader;
use PHPUnit\Framework\TestCase;

class AbstractEnumeratedHeaderTest extends TestCase
{
    protected function createEnumeratedHeader(?string $value = null): AbstractEnumeratedHeader
    {
        return new class($value) extends AbstractEnumeratedHeader {
        };
    }

    public function test_null()
    {
        $this->assertEquals([], $this->createEnumeratedHeader()->all());
    }

    public function test_empty()
    {
        $this->assertEquals([], $this->createEnumeratedHeader('')->all());
    }

}
