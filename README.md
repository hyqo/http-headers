# http-headers

![Packagist Version](https://img.shields.io/packagist/v/hyqo/http-headers?style=flat-square)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/hyqo/http-headers?style=flat-square)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/hyqo/http-headers/tests.yml?branch=main&label=tests&style=flat-square)

## Install

```sh
composer require hyqo/http-headers
```

## Usage

### Forwarded ([MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Forwarded))

```php
use Hyqo\Http\RequestHeaders;

$headers = new RequestHeaders(['Forwarded'=>'for=192.0.2.60; For="[2001:db8:cafe::17]:4711"; proto=https; host=foo.bar'])
$headers->forwarded->getFor(); //["192.0.2.60","[2001:db8:cafe::17]:4711"]
$headers->forwarded->getProto(); //"https"
$headers->forwarded->getHost(); //"foo.bar"
```

### X-Forwarded-For ([MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Forwarded-For))

```php
use Hyqo\Http\RequestHeaders;

$headers = new RequestHeaders(['X-Forwarded-For'=>'192.0.2.60, "[2001:db8:cafe::17]:4711"'])
$headers->forwarded->getFor()
```

```text
array(2) {
[0]=>
string(10) "192.0.2.60"
[1]=>
string(24) "[2001:db8:cafe::17]:4711"
}
```

### X-Forwarded-Proto ([MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Forwarded-Proto))

```php
use Hyqo\Http\RequestHeaders;

$headers = new RequestHeaders(['X-Forwarded-Proto'=>'https'])
$headers->forwarded->getProto()(); //https
```

### X-Forwarded-Prefix

```php
use Hyqo\Http\RequestHeaders;

$headers = new RequestHeaders(['X-Forwarded-Prefix'=>'/foo'])
$headers->forwarded->getPrefix(); //"/foo"
```
