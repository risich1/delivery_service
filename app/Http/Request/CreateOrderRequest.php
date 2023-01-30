<?php

namespace App\Http\Request;

class CreateOrderRequest extends AuthRequest {

    protected array $requireBodyFields = [
        'products',
        'address_a_id',
        'address_b_id',
        'customer_id',
        'seller_id',
    ];

}
