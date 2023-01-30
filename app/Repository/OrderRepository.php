<?php

namespace App\Repository;

use App\Entity\Order;
use App\Interface\ISource;

class OrderRepository extends Repository {

    protected string $table = 'orders';
    protected string $ordersProductsTable = 'orders_products';
    protected string $entity = Order::class;

    public function __construct(ISource $source) {
        parent::__construct($source, $this->table);
        $this->getQuery = "SELECT o.*, TRIM(BOTH ',' FROM GROUP_CONCAT(op.product_id)) as products FROM orders o LEFT JOIN orders_products op on op.order_id=o.id";
    }

    public function createOrder(Order $order): void {
        $data = $order->toArray();
        unset($data['products']);
        $orderId = $this->source->create($this->table, $data);
        foreach ($order->getProducts() as $product) {
            $this->source->create($this->ordersProductsTable, ['product_id' => (int) $product, 'order_id' => $orderId]);
        }
    }

    public function find(array $conditions = []): array
    {
        return parent::find($conditions);
    }

    public function getByRole(int $uid, string $role, int $id = 0): array|Order {
        $conditions = [
            ["o.{$role}_id", '=', $uid]
        ];
        if ($id) {
            $conditions[] = ['o.id', '=', $id];
        }
        $result = $this->find($conditions);
        return $id && $result ? array_shift($result) : $result;
    }

}
