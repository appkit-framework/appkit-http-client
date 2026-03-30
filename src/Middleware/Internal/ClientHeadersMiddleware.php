<?php

namespace AppKit\Http\Client\Middleware\Internal;

use AppKit\Http\Client\Middleware\ClientHttpMiddlewareInterface;

class ClientHeadersMiddleware implements ClientHttpMiddlewareInterface {
    public function processRequest($request, $next) {
        if(! $request -> hasHeader('User-Agent'))
            $request -> setHeader('User-Agent', 'AppKitHTTP');

        if(! $request -> hasHeader('Host')) {
            $hostHeader = $request -> getHost();
            $port = $request -> getPort();
            if($port !== null)
                $hostHeader .= ":$port";
            $request -> setHeader('Host', $hostHeader);
        }

        return $next($request);
    }
}
