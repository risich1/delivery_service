<?php

namespace App\Exceptions;

use App\Http\Response\Response;
use Exception;

class RateExceededException extends Exception {
    protected $message = 'Too many requests';
    protected $code = Response::HTTP_TOO_MANY_REQUESTS_CODE;
}
