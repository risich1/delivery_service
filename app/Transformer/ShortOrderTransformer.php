<?php

namespace App\Transformer;

use App\Entity\Order;
use App\Service\OrderService;

class ShortOrderTransformer {

    public function transform(array|Order $response): array {
        $responseForWork = is_array($response) ? $response : [$response];

        foreach ($responseForWork as $index => &$order) {
            $order = [
                'id' => $order->getId(),
                'status' => $order->getStatus(),
            ];
        }

        return !is_array($response) ? array_shift($responseForWork) : $responseForWork;
    }

}
