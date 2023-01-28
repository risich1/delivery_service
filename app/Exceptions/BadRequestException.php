<?php

namespace App\Exceptions;

use Exception;

class BadRequestException extends Exception {
    protected $code = 400;
}
