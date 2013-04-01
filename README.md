# Semi HTTP Cache for Zend Framework 2. [![Build Status](https://travis-ci.org/widmogrod/zf2-semi-http-cache.png?branch=master)](https://travis-ci.org/widmogrod/zf2-semi-http-cache)
## Introduction

## Time saving tips & tricks

Does your application use session?
1. Remember to start session in action like login, do not do it every time.
2. If session is started, headers like Set-Cookie & Cache-Control can be send.
To avoid caching headers in Varnish like Set-Cookie best decision will be to set:
```php
session_cache_limiter('no-cache');
session_cache_expire(0);
```
