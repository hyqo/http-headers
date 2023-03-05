<?php

namespace Hyqo\Http\Test;

use Hyqo\Http\Exception\HttpHeaderException;
use Hyqo\Http\Header\Request\Accept;
use Hyqo\Http\Header\Request\AcceptEncoding;
use Hyqo\Http\Header\Request\AcceptLanguage;
use Hyqo\Http\Header\Request\CacheControl;
use Hyqo\Http\Header\Request\Conditional;
use Hyqo\Http\Header\Request\ContentType;
use Hyqo\Http\Header\Request\Forwarded;
use Hyqo\Http\RequestHeaders;
use PHPUnit\Framework\TestCase;

class RequestHeadersTest extends TestCase
{
    protected function describeRichHeaders(): \Generator
    {
        yield ['accept', Accept::class];
        yield ['acceptEncoding', AcceptEncoding::class];
        yield ['acceptLanguage', AcceptLanguage::class];
        yield ['cacheControl', CacheControl::class];
        yield ['contentType', ContentType::class];
        yield ['if', Conditional::class];
        yield ['forwarded', Forwarded::class];
    }

    /**
     * @dataProvider describeRichHeaders
     */
    public function test_rich_header_class(string $name, string $class): void
    {
        $this->assertInstanceOf($class, RequestHeaders::createFrom([])->{$name});
    }

    /**
     * @dataProvider describeRichHeaders
     */
    public function test_rich_header_exists(string $name): void
    {
        $this->assertTrue(isset(RequestHeaders::createFrom([])->{$name}));
    }

    public function test_rich_header_does_not_exist(): void
    {
        $this->assertFalse(isset(RequestHeaders::createFrom([])->foo));
    }

    public function test_invalid_rich_header(): void
    {
        $this->expectException(HttpHeaderException::class);
        $this->expectExceptionMessage("Property foo doesn't exist");

        /** @noinspection PhpUndefinedFieldInspection */
        RequestHeaders::createFrom([])->foo;
    }

    public function test_magic_set(): void
    {
        $this->expectException(HttpHeaderException::class);
        $this->expectExceptionMessage("Property foo cannot be set");

        /** @noinspection PhpUndefinedFieldInspection */
        RequestHeaders::createFrom([])->foo = 'bar';
    }

    public function test_simple_headers(): void
    {
        foreach (
            [
                'HTTP_HOST' => $http_host = 'developer.mozilla.org',
                'HTTP_REFERER' => $http_referer = 'google.com',
                'HTTP_USER_AGENT' => $http_user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:50.0) Gecko/20100101 Firefox/50.0',
                'CONTENT_LENGTH' => $http_content_length = 123,
            ] as $name => $value
        ) {
            $_SERVER[$name] = $value;
        }

        $headers = RequestHeaders::createFromGlobals();

        $this->assertEquals($http_host, $headers->host);
        $this->assertEquals($http_referer, $headers->referer);
        $this->assertEquals($http_user_agent, $headers->userAgent);
        $this->assertEquals($http_content_length, $headers->contentLength);
    }

    public function test_all(): void
    {
        $headers = RequestHeaders::createFrom([
            'HTTP_ACCEPT' => 'text/html',
            'foo' => 'bar'
        ]);


        $this->assertEquals(['Accept' => 'text/html'], $headers->all());
    }

    /**
     * @dataProvider generateTestRichHeaderData
     */
    public function test_rich_header(
        array $list,
        array $asserts,
    ): void {
        $headers = RequestHeaders::createFrom($list);

        foreach ($asserts as [$expected, $getActualData]) {
            $this->assertEquals($expected, $getActualData($headers));
        }
    }

    public function generateTestRichHeaderData(): \Generator
    {
        yield [
            ['HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'],
            [
                [
                    ['text/html', 'application/xhtml+xml', 'application/xml', '*/*'],
                    fn(RequestHeaders $headers) => $headers->accept->all()
                ]
            ]
        ];

        yield [
            ['HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.5'],
            [
                [
                    ['en'],
                    fn(RequestHeaders $headers) => $headers->acceptLanguage->all()
                ]
            ]
        ];

        yield [
            ['HTTP_ACCEPT_ENCODING' => 'gzip, deflate, br'],
            [
                [
                    ['gzip', 'deflate', 'br'],
                    fn(RequestHeaders $headers) => $headers->acceptEncoding->all()
                ]
            ]
        ];

        yield [
            ['HTTP_CACHE_CONTROL' => 'max-age=0'],
            [
                [
                    0,
                    fn(RequestHeaders $headers) => $headers->cacheControl->getMaxAge()
                ]
            ]
        ];

        yield [
            ['HTTP_CONTENT_TYPE' => 'text/plain'],
            [
                [
                    'text/plain',
                    fn(RequestHeaders $headers) => $headers->contentType->mediaType
                ]
            ]
        ];

        yield [
            ['HTTP_IF_MODIFIED_SINCE' => 'Mon, 18 Jul 2016 02:36:04 GMT'],
            [
                [
                    'Mon, 18 Jul 2016 02:36:04 GMT',
                    fn(RequestHeaders $headers) => $headers->if->modifiedSince->format('D, d M Y H:i:s \G\M\T')
                ],
            ]
        ];

        yield [
            ['HTTP_IF_NONE_MATCH' => '"c561c68d0ba92bbb8b0fff2a9199f722e3a621a"'],
            [
                [
                    true,
                    fn(RequestHeaders $headers) => is_array($headers->if->noneMatch)
                ],
                [
                    true,
                    fn(RequestHeaders $headers) => array_key_exists(
                        'c561c68d0ba92bbb8b0fff2a9199f722e3a621a',
                        $headers->if->noneMatch
                    )
                ],
                [
                    'c561c68d0ba92bbb8b0fff2a9199f722e3a621a',
                    fn(RequestHeaders $headers
                    ) => $headers->if->noneMatch['c561c68d0ba92bbb8b0fff2a9199f722e3a621a']->value
                ],
                [
                    false,
                    fn(RequestHeaders $headers
                    ) => $headers->if->noneMatch['c561c68d0ba92bbb8b0fff2a9199f722e3a621a']->weak
                ],
            ]
        ];

        yield [
            [
                'HTTP_X_FORWARDED_HOST' => 'google.com',
                'HTTP_X_FORWARDED_FOR' => '127.0.0.1',
                'HTTP_X_FORWARDED_PROTO' => 'http',
                'HTTP_X_FORWARDED_PORT' => '123',
                'HTTP_X_FORWARDED_PREFIX' => '/foo',
            ],
            [
                [
                    'google.com',
                    fn(RequestHeaders $headers) => $headers->forwarded->host
                ],
                [
                    ['127.0.0.1'],
                    fn(RequestHeaders $headers) => $headers->forwarded->for
                ],
                [
                    'http',
                    fn(RequestHeaders $headers) => $headers->forwarded->proto
                ],
                [
                    123,
                    fn(RequestHeaders $headers) => $headers->forwarded->port
                ],
                [
                    '/foo',
                    fn(RequestHeaders $headers) => $headers->forwarded->prefix
                ],
            ]
        ];

        yield [
            [
                'HTTP_X_FORWARDED_HOST' => 'google.com',
                'HTTP_X_FORWARDED_FOR' => '127.0.0.1',
                'HTTP_X_FORWARDED_PROTO' => 'http',
                'HTTP_X_FORWARDED_PORT' => '123',
                'HTTP_X_FORWARDED_PREFIX' => '/foo',
                'HTTP_FORWARDED' => implode(';', [
                    'for=196.168.0.1,for=[::1]',
                    'proto=https',
                    'host=fb.com',
                ]),
            ],
            [
                [
                    'fb.com',
                    fn(RequestHeaders $headers) => $headers->forwarded->host
                ],
                [
                    ['196.168.0.1', '[::1]'],
                    fn(RequestHeaders $headers) => $headers->forwarded->for
                ],
                [
                    'https',
                    fn(RequestHeaders $headers) => $headers->forwarded->proto
                ],
                [
                    null,
                    fn(RequestHeaders $headers) => $headers->forwarded->port
                ],
                [
                    null,
                    fn(RequestHeaders $headers) => $headers->forwarded->prefix
                ],
            ]
        ];
    }

    public function test_get(): void
    {
        $headers = new RequestHeaders(['foo' => 'bar']);

        $this->assertEquals('bar', $headers->get('Foo'));
        $this->assertEquals('default', $headers->get('not_exists', 'default'));
        $this->assertNull($headers->get('not_exists'));
    }

    public function test_has(): void
    {
        $headers = new RequestHeaders(['foo' => 'bar']);

        $this->assertTrue($headers->has('Foo'));
        $this->assertFalse($headers->has('bar'));
    }
}
