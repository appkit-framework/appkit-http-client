<?php

namespace AppKit\Http\Client\ErrorParser;

interface HttpErrorParserInterface {
    public function parseError($response);
}
