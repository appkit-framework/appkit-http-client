<?php

namespace AppKit\Http\Client\Message;

use AppKit\Http\Message\AbstractHttpResponse;

class ClientHttpResponse extends AbstractHttpResponse {
    function __construct(
        $status,
        $headers,
        $bodyText
    ) {
        $this -> setStatus($status)
            -> setHeaders($headers)
            -> setBodyText($bodyText);
    }

    // Body

    public function setParsedBody($body) {
        return $this -> setBody($body);
    }
}
