<?php

namespace AppKit\Http\Client\Middleware\Internal;

use AppKit\Http\Client\Middleware\ClientHttpMiddlewareInterface;

class ClientHeadersMiddleware implements ClientHttpMiddlewareInterface {
    public function processRequest($request, $next) {
        if(! $request -> hasHeader('User-Agent'))
            $request -> setHeader('User-Agent', 'AppKitHTTP');

        return $next($request);
    }
}
