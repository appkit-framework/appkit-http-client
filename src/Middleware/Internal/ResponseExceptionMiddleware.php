<?php

namespace AppKit\Http\Client\Middleware\Internal;

use AppKit\Http\Client\Middleware\ClientHttpMiddlewareInterface;
use AppKit\Http\Client\Message\ClientHttpRedirect;
use AppKit\Http\Client\Message\ClientHttpError;

class ResponseExceptionMiddleware implements ClientHttpMiddlewareInterface {
    private $errorParser;

    function __construct($errorParser) {
        $this -> errorParser = $errorParser;
    }

    public function processRequest($request, $next) {
        $response = $next($request);

        $status = $response -> getStatus();

        if($status >= 400) {
            if($this -> errorParser)
                throw $this -> errorParser -> parseError($response);
        }

        else if($status >= 300)
            throw new ClientHttpRedirect($response);

        return $response;
    }

    public function setErrorParser($errorParser) {
        $this -> errorParser = $errorParser;
    }
}
