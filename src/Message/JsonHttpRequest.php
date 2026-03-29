<?php

namespace AppKit\Http\Client\Message;

use AppKit\Json\Json;

class JsonHttpRequest extends ClientHttpRequest {
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
            -> setHeader('Content-Type', 'application/json');
    }

    public function setBody($body) {
        parent::setBody($body);
        $this -> setBodyText(
            Json::encode($body)
        );
    }

}
