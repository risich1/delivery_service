<?php

namespace App\Http\Request;

use App\Exceptions\BadRequestException;

class SendOrderToCourierRequest extends AuthRequest {

    protected array $requireBodyFields = [
        'courier_id'
    ];

}
