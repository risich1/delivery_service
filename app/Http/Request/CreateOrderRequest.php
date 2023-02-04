<?php

namespace App\Http\Request;

use App\Exceptions\BadRequestException;

class CreateOrderRequest extends AuthRequest {

    protected array $requireBodyFields = [
        'products',
        'address_b_id',
        'customer_id',
        'seller_id',
    ];

    public function validate()
    {
        parent::validate();
        $body = $this->getBody();
        if ($body['customer_id'] == $body['seller_id']) {
            throw new BadRequestException("Customer ans seller must not are equal");
        }
    }

}
