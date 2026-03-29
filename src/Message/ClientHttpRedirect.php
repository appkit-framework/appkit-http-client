<?php

namespace AppKit\Http\Client\Message;

use AppKit\Http\Message\AbstractHttpRedirect;

class ClientHttpRedirect extends AbstractHttpRedirect {
    function __construct($response) {
        parent::__construct($response);
    }
}
