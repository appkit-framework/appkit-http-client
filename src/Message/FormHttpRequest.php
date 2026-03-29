<?php

namespace AppKit\Http\Client\Message;

class FormHttpRequest extends ClientHttpRequest {
    function __construct(
        $method,
        $url,
        $body,
        $headers = []
    ) {
        $this -> setMethod($method)
            -> setUrl($url)
            -> setBody($body)
            -> setHeaders($headers)
            -> setHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    public function setBody($body) {
        parent::setBody($body);
        $this -> setBodyText(
            http_build_query($body)
        );
    }

}
