<?php

namespace App\Http\Request;

class HandOrderToCourierRequest extends AuthRequest {

    protected array $requireBodyFields = [
        'courier_id'
    ];

}
