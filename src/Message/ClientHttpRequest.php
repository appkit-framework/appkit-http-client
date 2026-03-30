<?php

namespace AppKit\Http\Client\Message;

use AppKit\Http\Message\AbstractHttpRequest;

class ClientHttpRequest extends AbstractHttpRequest {
    private $scheme;
    private $host;
    private $port;

    function __construct(
        $method,
        $url,
        $queryParams = [],
        $headers = [],
        $bodyText = ''
    ) {
        $this -> setMethod($method)
            -> setUrl($url)
            -> setQueryParams($queryParams)
            -> setHeaders($headers)
            -> setBodyText($bodyText);
    }

    // Method

    public function setMethod($method) {
        return parent::setMethod($method);
    }

    // URL

    public function hasAbsoluteUrl() {
        return $this -> scheme && $this -> host;
    }

    public function getUrl() {
        $url = '';

        if($this -> hasAbsoluteUrl()) {
            $url .= $this -> scheme . '://' . $this -> host;
            if($this -> port !== null)
                $url .= ':' . $this -> port;
        }

        $url .= $this -> getTarget();

        return $url;
    }

    public function setUrl($url) {
        $parsedUrl = parse_url($url);

        if(isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) {
            $this -> setScheme($parsedUrl['scheme'])
                -> setHost($parsedUrl['host']);

            if(isset($parsedUrl['port']))
                $this -> setPort($parsedUrl['port']);
        }

        return $this -> setTarget($url);
    }

    public function getScheme() {
        return $this -> scheme;
    }

    public function setScheme($scheme) {
        $this -> scheme = $scheme;
        return $this;
    }

    public function getHost() {
        return $this -> host;
    }

    public function setHost($host) {
        $this -> host = $host;
        $this -> scheme ??= 'http';
        return $this;
    }

    public function getPort() {
        return $this -> port;
    }

    public function setPort($port) {
        $this -> port = intval($port);
        return $this;
    }

    // Target

    public function setTarget($target) {
        return parent::setTarget($target);
    }

    public function setPath($path) {
        return parent::setPath($path);
    }

    public function setQueryParam($name, $value) {
        return parent::setQueryParam($name, $value);
    }

    // Headers

    public function setHeader($name, $value) {
        return parent::setHeader($name, $value);
    }

    public function addHeader($name, $value) {
        return parent::addHeader($name, $value);
    }

    public function unsetHeader($name) {
        return parent::unsetHeader($name);
    }

    // Body

    public function setBodyText($bodyText) {
        return parent::setBodyText($bodyText);
    }
}
