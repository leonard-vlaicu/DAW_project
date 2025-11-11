<?php

namespace App;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Throwable;

class AccountNotVerifiedException extends BadCredentialsException {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
