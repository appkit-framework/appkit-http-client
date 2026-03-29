<?php

namespace AppKit\Http\Client\Middleware\Internal;

use AppKit\Http\Client\Middleware\ClientHttpMiddlewareInterface;

use AppKit\Json\Json;

use Throwable;

class ResponseBodyParserMiddleware implements ClientHttpMiddlewareInterface {
    public function processRequest($request, $next) {
        $response = $next($request);

        [ $type ] = explode(';', strtolower($response -> getHeaderLine('Content-Type')));

        if($type == 'application/json') {
            try {
                $response -> setParsedBody(Json::decode($response -> getBodyText()));
            } catch(Throwable $e) {}
        }

        return $response;
    }
}
