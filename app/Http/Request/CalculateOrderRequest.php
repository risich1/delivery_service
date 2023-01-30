<?php

namespace App\Http\Request;

class CalculateOrderRequest extends AuthRequest {

    protected array $requireBodyFields = [
        'point_a',
        'point_b',
    ];

}
