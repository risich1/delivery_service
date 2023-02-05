<?php

namespace App;

use App\Entity\Order;
use App\Entity\User;
use App\Interface\ISource;
use Faker\Factory;
use App\Repository\OrderRepository;

class Seeder {

    protected ISource $db;

    public function __construct(ISource $db)
    {
        $this->db = $db;
    }

    public function run(): void {
        $faker = Factory::create();

        $users = [];
        $addresses = [];
        $products = [];

        for ($i = 0; $i < 30; $i++) {
            $coords = $faker->localCoordinates();
            $addresses[] = [
                'title' => $faker->streetAddress(),
                'lat' => (string) $coords['latitude'],
                'lng' => (string) $coords['longitude']
            ];
        }

        foreach ([User::SELLER_ROLE, User::COURIER_ROLE, User::CUSTOMER_ROLE] as $key => $role) {
            for ($i = 0; $i < 3; $i++) {
                $users[] =  [
                    'full_name' => "{$faker->lastName()} {$faker->firstName()} ($role)",
                    'phone' => '+3753355566' . $key . $i,
                    'u_role' => $role,
                    'password' => password_hash('password123', PASSWORD_BCRYPT),
                    'default_address_id' => $role === User::SELLER_ROLE ? ($i + 1) : NULL
                ];
            }
        }

        for ($i = 0; $i < 30; $i++) {
            $products[] = ['seller_id' => rand(1,3), 'name' => "Product {$i}"];
        }

        $this->db->createBatch('addresses', $addresses);
        $this->db->createBatch('users', $users);
        $this->db->createBatch('products', $products);

        $orders = [
                [
                    'seller_id' => 1,
                    'customer_id' => 7,
                    'courier_id' => 5,
                    'address_a_id' => 1,
                    'address_b_id' => 6,
                    'products' => [1, 2],
                    'status' => 'handed_courier',
                    'cost' => 10
                ],
                [
                    'seller_id' => 1,
                    'customer_id' => 8,
                    'address_a_id' => 1,
                    'address_b_id' => 7,
                    'products' => [3, 4],
                    'cost' => 8
                ],
                [
                    'seller_id' => 1,
                    'customer_id' => 9,
                    'address_a_id' => 1,
                    'address_b_id' => 4,
                    'products' => [7, 1],
                    'cost' => 5
                ],
                [
                    'seller_id' => 2,
                    'customer_id' => 8,
                    'address_a_id' => 1,
                    'address_b_id' => 6,
                    'products' => [3, 2],
                    'cost' => 10
                ],
                [
                    'seller_id' => 2,
                    'customer_id' => 9,
                    'address_a_id' => 1,
                    'address_b_id' => 7,
                    'products' => [8, 4],
                    'cost' => 8
                ],
                [
                    'seller_id' => 2,
                    'customer_id' => 7,
                    'courier_id' => 4,
                    'address_a_id' => 1,
                    'address_b_id' => 5,
                    'products' => [7, 2],
                    'status' => 'handed_courier',
                    'cost' => 15
                ],
            ];

        $orderRepository = new OrderRepository($this->db);

        foreach ($orders as $order) {
            $orderRepository->createOrder(new Order($order));
        }

    }

}
