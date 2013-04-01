# Semi HTTP Cache for Zend Framework 2 [![Build Status](https://travis-ci.org/widmogrod/zf2-semi-http-cache.png?branch=master)](https://travis-ci.org/widmogrod/zf2-semi-http-cache)
## Exclamation

This module is still in development phase.

## Introduction

TBD

### What issues it solves?
- Wrong `Last-Modified` date when your application is running on Apache.
  This date in taken from index.php modification date which is not accurate
  and can cause that browser won't cache response.

### What benefits it brings?
- Handling `If-Modified-Since` which:
  - Reducing bandwidth. If browser has valid cached data then only 304 header is send.
  - Speed up response time, by omitting dispatch event if browser cache is not stale.
- Providing out of the box `Cache-Control` management.
- Providing more accurate Last-Modified date solution (but not perfect).

### Why `semi` HTTP cache?
Because things like `If-Modified-Since` are calculated base of `Cache-Control: max-age`
and not base to real modification date of requested entity.


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

By default, HTTP Cache is disabled if you wan to enable it you should enable it by adding minimal configuration
or copying file config/zf2-semi-http-cache.local.php.dist to your application configuration.

```php
<?php
return array(
    'zf2-semi-http-cache' => array(
        'enabled' => false,
        'default' => array(
             'max-age'  => 600,   // 10min in browser
        ),
    ),
);
```

## Time saving tips & tricks

Does your application use session?
 1. Remember to start session in action like login, do not do it every time.
 2. If session is started, headers like Set-Cookie & Cache-Control can be send.
    To avoid caching headers in Varnish like Set-Cookie best decision will be to set:

```php
session_cache_limiter('no-cache');
session_cache_expire(0);
```
