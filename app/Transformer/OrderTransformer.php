<?php

namespace App\Transformer;


use App\Entity\Order;
use App\Repository\AddressRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;

class OrderTransformer {

    protected UserRepository $userRepository;
    protected ProductRepository $productRepository;
    protected AddressRepository $addressRepository;

    public function __construct(UserRepository $userRepository, ProductRepository $productRepository, AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }

    public function transform(array|Order $response): array|Order {

        $productIds = [];
        $addressIds = [];

        if (is_array($response)) {
            foreach ($response as $entity) {
                $productIds =  array_merge($productIds, $entity->getProducts());
                $addressIds = array_merge($addressIds, [$entity->getAddressAId(), $entity->getAddressBId()]);
            }
        } else {
            $productIds =  array_merge($productIds, $response->getProducts());
            $addressIds = array_merge($addressIds, [$response->getAddressAId(), $response->getAddressBId()]);
        }

        $products = $this->productRepository->find([
            ['id', 'IN', $productIds]
        ]);

        $address = $this->productRepository->find([
            ['id', 'IN', $addressIds]
        ]);


        return [

        ];
    }

}
