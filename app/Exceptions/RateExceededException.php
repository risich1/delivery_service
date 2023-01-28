<?php

namespace App\Exceptions;

use Exception;

class RateExceededException extends Exception {
    protected $message = 'Too many requests';
    protected $code = 429;
}
