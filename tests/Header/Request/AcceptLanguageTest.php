<?php

namespace Hyqo\Http\Test\Header\Request;

use Hyqo\Http\Header\Request\AcceptLanguage;
use PHPUnit\Framework\TestCase;

class AcceptLanguageTest extends TestCase
{
    public function test_all(): void
    {
        $accept = new AcceptLanguage('en-us,en-uk;q=0.9,*;q=0.8');

        $this->assertEquals(['en', '*'], $accept->all());
    }

    public function test_invalid(): void
    {
        $accept = new AcceptLanguage('foo');

        $this->assertEquals([], $accept->all());
    }
}
