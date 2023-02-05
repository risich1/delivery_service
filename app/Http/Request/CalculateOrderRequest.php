<?php

namespace App\Http\Request;

class CalculateOrderRequest extends AuthRequest {

    protected array $requireBodyFields = [
        'address_a_id',
        'address_b_id',
    ];

}
