<?php

namespace AppKit\Http\Client\Middleware\Internal;

use AppKit\Http\Client\Middleware\ClientHttpMiddlewareInterface;
use AppKit\Http\Client\Message\ClientHttpRedirect;
use AppKit\Http\Client\Message\ClientHttpError;

class ResponseExceptionMiddleware implements ClientHttpMiddlewareInterface {
    public function processRequest($request, $next) {
        $response = $next($request);

        $status = $response -> getStatus();
        if($status < 300)
            return $response;
        if($status < 400)
            throw new ClientHttpRedirect($response);
        throw new ClientHttpError($response);
    }
}
