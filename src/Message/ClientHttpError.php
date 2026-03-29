<?php

namespace AppKit\Http\Client\Message;

use AppKit\Http\Message\AbstractHttpError;

class ClientHttpError extends AbstractHttpError {
    function __construct($response) {
        parent::__construct($response);
    }
}
