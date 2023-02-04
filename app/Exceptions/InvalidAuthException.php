<?php

namespace App\Exceptions;

use App\Http\Response\Response;
use Exception;

class InvalidAuthException extends Exception {
    protected $message = 'Invalid credentials';
    protected $code = Response::HTTP_UNAUTHORIZED_CODE;
}
