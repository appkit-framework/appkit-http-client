<?php

namespace AppKit\Http\Client\ErrorParser;

use AppKit\Http\Client\Message\ClientHttpError;

class DefaultErrorParser implements HttpErrorParserInterface {
    public function parseError($response) {
        return new ClientHttpError($response);
    }
}
