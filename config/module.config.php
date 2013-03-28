<?php
return array(
    'zf2-semi-http-cache' => array(
        'default' => array(
            /**
             * https://www.varnish-software.com/static/book/VCL_Basics.html#the-initial-value-of-beresp-ttl
             *
             * A sensible approach is to use the s-maxage variable in the Cache-Control header to instruct Varnish to cache, then have Varnish remove that variable before sending it to clients using regsub() in vcl_fetch.
             * That way, you can safely set max-age to what cache duration the clients should use and s-maxage for Varnish without affecting intermediary caches.
             *
             * Warning: Varnish, browsers and intermediary will parse the Age response header. If you stack multiple Varnish servers in front of each other, this means that setting s-maxage=300 will mean that the object really will be cached for only 300 seconds throughout all Varnish servers.
             * On the other hand, if your web server sends Cache-Control: max-age=300, s-maxage=3600 and you do not remove the Age response header, Varnish will send an Age-header that exceeds the max-age of the objects, which will cause browsers to not cache the content.
             */
            's-maxage' => 3600,  // 1h
            'max-age'  => 600,   // 10min in browser
        ),
    ),
);