<?php

namespace App\Service;

use App\Entity\Entity;
use App\Entity\Order;
use App\Entity\User;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use JetBrains\PhpStorm\ArrayShape;

class OrderService {

    protected OrderRepository $repository;
    protected UserRepository $userRepository;
    protected ProductRepository $productRepository;
    protected AddressRepository $addressRepository;

    public function __construct(
        OrderRepository $repository,
        UserRepository $userRepository,
        ProductRepository $productRepository,
        AddressRepository $addressRepository,
    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->addressRepository = $addressRepository;
        $this->productRepository = $productRepository;
    }

    public function calculateCost(User $user, int $pointA, int $pointB): int {
        return rand(1, 20);
    }

    /**
     * @throws BadRequestException
     */
    public function createOrder(array $data): void {
        $seller = $this->userRepository->getById($data['seller_id']);
        $customer = $this->userRepository->getById($data['customer_id']);

        if ($seller->getURole() !== User::SELLER_ROLE) {
            throw new BadRequestException('Invalid seller');
        }

        if ($customer->getURole() !== User::CUSTOMER_ROLE) {
            throw new BadRequestException('Invalid customer');
        }

        $order = new Order($data);
        $order->setAddressAId($seller->getDefaultAddressId());
        $this->repository->createOrder($order);
    }

    #[ArrayShape(['users' => "array", 'products' => "array", 'addresses' => "array"])]
    protected function loadChildEntities(array $orderCollection): array {
        $productIds = [];
        $addressIds = [];
        $userIds = [];

        foreach ($orderCollection as $order) {
            $productIds = array_merge($productIds, [$order->getCourierId(), $order->getCustomerId(), $order->getSellerId()]);
            $addressIds = array_merge($addressIds, [$order->getAddressAId(), $order->getAddressBId()]);
            $userIds = array_merge($userIds, [$order->getCourierId(), $order->getCustomerId(), $order->getSellerId()]);
        }

        return [
            'users' => $this->userRepository->getByIds($userIds),
            'products' => $this->productRepository->getByIds($productIds),
            'addresses' => $this->addressRepository->getByIds($addressIds)
        ];
    }

    protected function filterChildEntities($compareValue, array $entities): array|Entity|null {
        $filtered = array_filter($entities, function ($entity) use ($compareValue) {
            return is_array($compareValue) ? in_array($entity->getId(), $compareValue) : $entity->getId() === $compareValue;
        });

        return is_array($compareValue) ? $filtered : array_shift($filtered);
    }

    public function getChildEntitiesCollection(array $orders): array {
        $childEntitiesCollection = $this->loadChildEntities($orders);
        $users = $childEntitiesCollection['users'];
        $products = $childEntitiesCollection['products'];
        $addresses = $childEntitiesCollection['addresses'];
        $result = [];
        foreach ($orders as $index => $order) {
            $childEntityIds = [
                'products' => [$order->getProducts(), $products],
                'addressA' => [$order->getAddressAId(), $addresses],
                'addressB' => [$order->getAddressBId(), $addresses],
                'seller' => [$order->getSellerId(), $users],
                'customer' => [$order->getCustomerId(), $users],
                'courier' => [$order->getCourierId(), $users],
            ];
            foreach ($childEntityIds as $key => $childEntityId) {
                $result[$index][$key] =  $this->filterChildEntities($childEntityId[0], $childEntityId[1]);
            }
        }
        return $result;
    }

    /**
     * @throws NotFoundException
     */
    public function getOrderById(int $id, User $user): Order {
        $order = $this->repository->getByRole($user->getId(), $user->getUrole(), $id);

        if (!$order) {
            throw new NotFoundException('Order not found');
        }

        return $order;
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function handOrderToCourier(int $orderId, User $seller, int $courierId): void {
        $courier = $this->userRepository->getById($courierId);
        $order = $this->repository->getByRole($seller->getId(), $seller->getUrole(), $orderId);

        if (!$order || $order->getSellerId() !== $seller->getId()) {
            throw new NotFoundException('Order not found');
        }

        if ($courier->getURole() !== User::COURIER_ROLE) {
            throw new BadRequestException('Invalid courier id');
        }

        $order->setCourierId($courierId);
        $order->setStatus(Order::STATUS_HANDED_COURIER);

        $saveData = $order->toArray();
        unset($saveData['products']);

        $this->repository->updateOne($orderId, $saveData);
    }

    public function getOrderList(User $user): array {
        return $this->repository->getByRole($user->getId(), $user->getUrole());
    }
}
