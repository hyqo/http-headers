<?php

namespace PHPSTORM_META {

    registerArgumentsSet(
        'conditional_headers',
        'If-Match',
        'If-Modified-Since',
        'If-None-Match',
        'If-Range',
        'If-Unmodified-Since'
    );

    registerArgumentsSet(
        'x_forwarded_headers',
        'X-Forwarded-For',
        'X-Forwarded-Host',
        'X-Forwarded-Port',
        'X-Forwarded-Prefix',
        'X-Forwarded-Proto',
    );

    registerArgumentsSet(
        'request_headers',
        'Accept',
        'Accept-Language',
        'Accept-Encoding',
        'Cache-Control',
        'Content-Type',
        'Forwarded',
        'Host',
        'Referer',
        'User-Agent',
    );

    registerArgumentsSet(
        'response_headers',
        'Cache-Control',
        'Content-Disposition',
        'Content-Encoding',
        'Content-Language',
        'Content-Length',
        'Content-Type',
        'ETag',
        'Location',
    );

    registerArgumentsSet(
        'media_types',
        'text/plain',
        'text/html',
        'application/pdf',
        'application/json',
        'application/x-www-form-urlencoded',
        'application/xml',
        'application/zip',
        'multipart/form-data',
    );

    registerArgumentsSet(
        'charsets',
        'utf-8',
    );

    expectedArguments(\Hyqo\Http\Header\Request\Conditional::set(), 0, argumentsSet('conditional_headers'));
    expectedArguments(\Hyqo\Http\Header\Request\Forwarded::setX(), 0, argumentsSet('x_forwarded_headers'));

    expectedArguments(
        \Hyqo\Http\RequestHeaders::get(),
        0,
        argumentsSet('request_headers'),
        argumentsSet('conditional_headers'),
        argumentsSet('x_forwarded_headers'),
    );
    expectedArguments(
        \Hyqo\Http\RequestHeaders::set(),
        0,
        argumentsSet('request_headers'),
        argumentsSet('conditional_headers'),
        argumentsSet('x_forwarded_headers'),
    );

    expectedArguments(
        \Hyqo\Http\ResponseHeaders::set(),
        0,
        argumentsSet('response_headers'),
    );

    expectedArguments(
        \Hyqo\Http\Response::setHeader(),
        0,
        argumentsSet('response_headers'),
    );

    expectedArguments(\Hyqo\Http\ResponseHeaders::setContentType(), 0, argumentsSet('media_types'));
    expectedArguments(\Hyqo\Http\ResponseHeaders::setContentType(), 1, argumentsSet('charsets'));

    expectedArguments(\Hyqo\Http\Response::setContentType(), 0, argumentsSet('media_types'));
    expectedArguments(\Hyqo\Http\Response::setContentType(), 1, argumentsSet('charsets'));

    expectedReturnValues(\Hyqo\Http\Request::getContentType(), argumentsSet('media_types'));

    expectedArguments(\Hyqo\Http\Header\Response\ContentType::__construct(), 0, argumentsSet('media_types'));
    expectedArguments(\Hyqo\Http\Header\Response\ContentType::__construct(), 1, argumentsSet('charsets'));
}
