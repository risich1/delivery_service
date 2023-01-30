<?php

namespace App;

use App\Entity\User;
use App\Interface\ISource;
use Faker\Factory;

class Seeder {

    protected ISource $db;

    public function __construct(ISource $db)
    {
        $this->db = $db;
    }

    public function run(): void {
        $faker = Factory::create();

        for ($i = 0; $i < 30; $i++) {
            $coords = $faker->localCoordinates();
            $address = [
                'title' => $faker->streetAddress(),
                'lat' => (string) $coords['latitude'],
                'lng' => (string) $coords['longitude']
            ];

            $this->db->create('addresses', $address);
        }

        $sellerIds = [];

        foreach ([User::COURIER_ROLE, User::CUSTOMER_ROLE, User::SELLER_ROLE] as $key => $role) {
            for ($i = 0; $i < 10; $i++) {
                $user =  [
                    'full_name' => "{$faker->lastName()} {$faker->firstName()} ($role)",
                    'phone' => '+37533' . rand(1000000, 9999999),
                    'u_role' => $role,
                    'password' => 'password123'
                ];

                if ($role === User::SELLER_ROLE) {
                    $user['default_address_id'] = rand(1,30);
                }

                $id = $this->db->create('users', $user);

                if ($role === User::SELLER_ROLE) {
                    $sellerIds[] = $id;
                }
            }
        }

        for ($i = 0; $i < 10; $i++) {
            $this->db->create('products', ['seller_id' => $sellerIds[$i], 'name' => "Product {$faker->numerify()}"]);
        }

    }
}
