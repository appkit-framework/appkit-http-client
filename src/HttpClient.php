<?php

namespace AppKit\Http\Client;

use AppKit\Http\Client\Message\ClientHttpRequest;
use AppKit\Http\Client\Message\JsonHttpRequest;
use AppKit\Http\Client\Message\FormHttpRequest;
use AppKit\Http\Client\Message\ClientHttpResponse;
use AppKit\Http\Client\Message\ClientHttpError;
use AppKit\Http\Client\Message\ClientHttpRedirect;
use AppKit\Http\Client\Middleware\Internal\ClientHeadersMiddleware;
use AppKit\Http\Client\Middleware\Internal\ResponseExceptionMiddleware;
use AppKit\Http\Client\Middleware\Internal\ResponseBodyParserMiddleware;

use AppKit\Http\Middleware\HttpMiddlewarePipeline;
use function AppKit\Async\await;

use Throwable;
use React\Http\Browser;

class HttpClient {
    private $baseUrl;
    private $browser;
    private $pipeline;

    function __construct(
        $baseUrl = null,
        $options = []
    ) {
        if($baseUrl)
            $this -> baseUrl = $this -> parseBaseUrl($baseUrl);

        $this -> browser = $this -> createBrowser($options);

        $this -> pipeline = new HttpMiddlewarePipeline(function($request) {
            return $this -> sendRequest($request);
        });
        $this -> pipeline -> addMiddleware(
            new ClientHeadersMiddleware()
        ) -> addMiddleware(
            new ResponseExceptionMiddleware()
        ) -> addMiddleware(
            new ResponseBodyParserMiddleware()
        );
    }

    public function addMiddleware($middleware) {
        $this -> pipeline -> addMiddleware($middleware);
        return $this;
    }

    // Send

    public function send($request, $options = []) {
        if(! $request -> hasAbsoluteUrl()) {
            if(! $this -> baseUrl)
                throw new HttpClientException('Request URL is relative and no base URL is set');

            $request -> setScheme($this -> baseUrl['scheme'])
                -> setHost($this -> baseUrl['host'])
                -> setPort($this -> baseUrl['port']);

            if($this -> baseUrl['path'])
                $request -> setPath($this -> baseUrl['path'] . $request -> getPath());
        }

        $request -> setAttribute(self::class, $options);

        return $this -> pipeline -> processRequest($request);
    }

    // Request

    public function request(
        $method,
        $url,
        $queryParams = [],
        $headers = [],
        $bodyText = '',
        $options = []
    ) {
        return $this -> send(
            new ClientHttpRequest($method, $url, $queryParams, $headers, $bodyText),
            $options
        );
    }

    public function requestJson(
        $method,
        $url,
        $body,
        $queryParams = [],
        $headers = [],
        $options = []
    ) {
        return $this -> send(
            new JsonHttpRequest($method, $url, $body, $queryParams, $headers),
            $options
        );
    }

    public function requestForm(
        $method,
        $url,
        $body,
        $queryParams = [],
        $headers = [],
        $options = []
    ) {
        return $this -> send(
            new FormHttpRequest($method, $url, $body, $queryParams, $headers),
            $options
        );
    }

    // Get

    public function get($url, $queryParams = [], $headers = [], $options = []) {
        return $this -> request('GET', $url, $queryParams, $headers, options: $options);
    }

    // Post

    public function post($url, $queryParams = [], $headers = [], $bodyText = '', $options = []) {
        return $this -> request('POST', $url, $queryParams, $headers, $bodyText, $options);
    }

    public function postJson($url, $body, $queryParams = [], $headers = [], $options = []) {
        return $this -> requestJson('POST', $url, $body, $queryParams, $headers, $options);
    }

    public function postForm($url, $body, $queryParams = [], $headers = [], $options = []) {
        return $this -> requestForm('POST', $url, $body, $queryParams, $headers, $options);
    }

    // Put

    public function put($url, $queryParams = [], $headers = [], $bodyText = '', $options = []) {
        return $this -> request('PUT', $url, $queryParams, $headers, $bodyText, $options);
    }

    public function putJson($url, $body, $queryParams = [], $headers = [], $options = []) {
        return $this -> requestJson('PUT', $url, $body, $queryParams, $headers, $options);
    }

    public function putForm($url, $body, $queryParams = [], $headers = [], $options = []) {
        return $this -> requestForm('PUT', $url, $body, $queryParams, $headers, $options);
    }

    // Delete

    public function delete($url, $queryParams = [], $headers = [], $bodyText = '', $options = []) {
        return $this -> request('DELETE', $url, $queryParams, $headers, $bodyText, $options);
    }

    public function deleteJson($url, $body, $queryParams = [], $headers = [], $options = []) {
        return $this -> requestJson('DELETE', $url, $body, $queryParams, $headers, $options);
    }

    public function deleteForm($url, $body, $queryParams = [], $headers = [], $options = []) {
        return $this -> requestForm('DELETE', $url, $body, $queryParams, $headers, $options);
    }

    // Patch

    public function patch($url, $queryParams = [], $headers = [], $bodyText = '', $options = []) {
        return $this -> request('PATCH', $url, $queryParams, $headers, $bodyText, $options);
    }

    public function patchJson($url, $body, $queryParams = [], $headers = [], $options = []) {
        return $this -> requestJson('PATCH', $url, $body, $queryParams, $headers, $options);
    }

    public function patchForm($url, $body, $queryParams = [], $headers = [], $options = []) {
        return $this -> requestForm('PATCH', $url, $body, $queryParams, $headers, $options);
    }

    // Head

    public function head($url, $queryParams = [], $headers = [], $options = []) {
        return $this -> request('HEAD', $url, $queryParams, $headers, options: $options);
    }

    // Options

    public function options($url, $queryParams, $headers = [], $options = []) {
        return $this -> request('OPTIONS', $url, $queryParams, $headers, options: $options);
    }

    // Internal

    private function sendRequest($request) {
        try {
            $options = $request -> getAttribute(self::class);
            $browser = $this -> getBrowser($options);

            $psrResponse = await($browser -> request(
                $request -> getMethod(),
                $request -> getUrl(),
                $request -> getHeaders(),
                $request -> getBodyText()
            ));
        } catch(Throwable $e) {
            throw new HttpClientException($e -> getMessage(), previous: $e);
        }

        return new ClientHttpResponse(
            $psrResponse -> getStatusCode(),
            $psrResponse -> getHeaders(),
            (string) $psrResponse -> getBody()
        );
    }

    private function getBrowser($options) {
        if(empty($options))
            return $this -> browser;
        return $this -> createBrowser($options);
    }

    private function createBrowser($options) {
        return new Browser()
            -> withRejectErrorResponse(false)
            -> withFollowRedirects($options['followRedirects'] ?? true)
            -> withTimeout($options['timeout'] ?? 10);
    }

    private function parseBaseUrl($baseUrl) {
        $parsed = parse_url($baseUrl);

        if(!isset($parsed['scheme']))
            throw new HttpClientException('Missing base URL component: scheme');
        if(!isset($parsed['host']))
            throw new HttpClientException('Missing base URL component: host');

        if(isset($parsed['path'])) {
            $path = rtrim($parsed['path'], '/');
            if($path == '')
                $path = null;
        } else {
            $path = null;
        }

        return [
            'scheme' => $parsed['scheme'],
            'host' => $parsed['host'],
            'port' => $parsed['port'] ?? null,
            'path' => $path
        ];
    }
}
