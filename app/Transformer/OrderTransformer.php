<?php

namespace App\Transformer;

use App\Entity\Order;
use App\Service\OrderService;

class OrderTransformer {

    protected UserTransformer $userTransformer;
    protected AddressTransformer $addressTransformer;
    protected ProductTransformer $productTransformer;
    protected OrderService $orderService;

    public function __construct(
        UserTransformer $userTransformer,
        AddressTransformer $addressTransformer,
        ProductTransformer $productTransformer,
        OrderService $orderService,
    ) {
        $this->orderService = $orderService;
        $this->userTransformer = $userTransformer;
        $this->addressTransformer = $addressTransformer;
        $this->productTransformer = $productTransformer;
    }

    public function transform(array|Order $response): array {
        $responseForWork = is_array($response) ? $response : [$response];
        $childEntitiesCollection = $this->orderService->getChildEntitiesCollection($responseForWork);
        foreach ($responseForWork as $index => &$order) {
            $childEntities = $childEntitiesCollection[$index];
            $order = [
                'id' => $order->getId(),
                'status' => $order->getStatus(),
                'addressA' => $this->addressTransformer->transform($childEntities['addressA']),
                'addressB' => $this->addressTransformer->transform($childEntities['addressB']),
                'products' => $this->productTransformer->transform($childEntities['products']),
                'seller' => $this->userTransformer->transform($childEntities['seller']),
                'customer' => $this->userTransformer->transform($childEntities['customer']),
                'courier' => $this->userTransformer->transform($childEntities['courier'])
            ];
        }

        return !is_array($response) ? array_shift($responseForWork) : $responseForWork;
    }

}
