<?php

namespace App\Exceptions;

use Exception;

class InvalidAuthException extends Exception {
    protected $message = 'Invalid credentials';
    protected $code = 401;
}
