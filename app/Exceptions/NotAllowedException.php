<?php

namespace App\Exceptions;

use App\Http\Response\Response;
use Exception;

class NotAllowedException extends Exception {
    protected $message = 'Not allowed';
    protected $code = Response::HTTP_NOT_ALLOWED;
}
