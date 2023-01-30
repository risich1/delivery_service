<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\User;
use App\Exceptions\NotFoundException;
use App\Repository\OrderRepository;

class OrderService {

    protected OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function calculateCost(int $pointA, int $pointB): int {
        return rand(1, 10);
    }

    public function createOrder(array $data): void {
        $this->repository->createOrder(new Order($data));
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

    public function getOrderList(User $user): array {
        return $this->repository->getByRole($user->getId(), $user->getUrole());
    }
}
