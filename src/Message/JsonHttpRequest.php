<?php

namespace AppKit\Http\Client\Message;

use AppKit\Json\Json;

class JsonHttpRequest extends ClientHttpRequest {
    function __construct(
        $method,
        $url,
        $body,
        $queryParams = [],
        $headers = []
    ) {
        $this -> setMethod($method)
            -> setUrl($url)
            -> setQueryParams($queryParams)
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
