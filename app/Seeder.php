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
    }

}
