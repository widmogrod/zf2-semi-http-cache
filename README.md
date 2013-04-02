# Semi HTTP Cache for Zend Framework 2 [![Build Status](https://travis-ci.org/widmogrod/zf2-semi-http-cache.png?branch=master)](https://travis-ci.org/widmogrod/zf2-semi-http-cache)
## Exclamation

This module is still in development phase.

## Introduction

### What issues it solves?
- Wrong `Last-Modified` date when your application is running on Apache.
  This date in taken from index.php modification date which is not accurate
  and can cause that browser won't cache response.

### What benefits it brings?
Enabling browser cache workflow.
- Handling `If-Modified-Since` which:
  - Reducing bandwidth. If browser has valid cached data then only 304 header is send.
  - Speed up response time, by omitting dispatch event if browser cache is not stale.
- Providing out of the box `Cache-Control` management.
- Providing more accurate `Last-Modified` date solution (but not perfect).

### Why `semi` HTTP cache?
Because things like `If-Modified-Since` are calculated base of `Cache-Control: max-age`
and not base to real modification date of requested entity.
The `max-age` is set explicit per action or global for application.
It's big simplification, but as solution out of the box is quite effective.
If you looking for something more bespoke I recommend The Symfony framework approach:
http://symfony.com/doc/2.0/book/http_cache.html

### Simple workflow ###

  1. Browser request resource `/data.json`.
     In application max-age is set for this resource for 60 seconds.
     Application return response with headers:
     - `Last-Modified: 2013-04-01 10:00:00 GMT`
     - `Cache-Control: max-age=60`

  2. a) Browser try to request resource `/data.json` after 10 seconds,
     but it already has cached content so it return data from browser cache.
     No request is made.

  2. b) Sometimes may happen that browser try to request resource `/data.json` after 10 seconds,
     and it want to validate if cached resource is stale.
     So it sends request wth header `If-Modified-Since : 2013-04-01 10:00:00 GMT`.
     But application knows that valid lifetime for this resource is 60 seconds
     so application return response without body but with headers:
     - `304 Not Modified`
     - `Last-Modified: 2013-04-01 10:00:00 GMT` - note that last modified is old one.

  3. Browser try to request resource after 70 seconds,
     but it already has cached content so it sends request
     with header `If-Modified-Since : 2013-04-01 10:00:00 GMT` to validate if cached resource is stale.
     Application knows that browser cache is stage so it's
     returning respond with headers and with new response body:
     - `Last-Modified: 2013-04-01 10:01:10 GMT` - note new last modified time.
     - `Cache-Control: max-age=60`

## Installation

  1. `cd my/project/directory`
  2. Create a `composer.json` file with following content:

``` json
{
    "require": {
        "widmogrod/zf2-semi-http-cache": "dev-master"
    }
}
```

  3. Run `php composer.phar install`
  4. Open ``my/project/folder/configs/application.config.php`` and add ``'WidHttpCache'`` to your ``'modules'`` parameter.

## Configuration

By default, HTTP Cache is disabled if you wan to enable it you should enable it by copying configuration:

```sh
cp vendor/widmogrod/zf2-semi-http-cache/config/zf2-semi-http-cache.local.php config/autoload/
```

Or, by adding this config entry to your local.php configuration file:

```php
<?php
return array(
    'zf2-semi-http-cache' => array(
        'enabled' => true,
        'default' => array(
             'max-age'  => 600,   // 10min in browser
        ),
    ),
);
```

## Todo

 1. create simple UML diagram describing workflow
 2. example with Varnish cache.

## Time saving tips & tricks

Does your application use session?
 1. Remember to start session in action like login, do not do it every time.
 2. If session is started, headers like Set-Cookie & Cache-Control can be send.
    To avoid caching headers in Varnish like Set-Cookie best decision will be to set:

```php
session_cache_limiter('no-cache');
session_cache_expire(0);
```
