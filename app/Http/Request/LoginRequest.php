<?php

namespace App\Http\Request;

class LoginRequest extends Request {

    protected array $requireBodyFields = ['phone', 'password'];

}
