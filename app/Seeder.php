<?php

namespace App;

class Seeder {

    protected DB $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    public function run(): void {

        $roleIds = [];

        foreach (['seller', 'customer', 'courier'] as $role) {
            $roleIds[] = $this->db->create('roles', ['role_name' => $role]);
        }

        $users = [
            [
                'full_name' => 'Seller Sellerovich',
                'email' => 'seller@seller.com',
                'password' => password_hash("seller_password", PASSWORD_BCRYPT),
            ],
            [
                'full_name' => 'Customer Customerovich',
                'email' => 'customer@customer.com',
                'password' => password_hash("customer_password", PASSWORD_BCRYPT)
            ],
            [
                'full_name' => 'Courier Courierovich',
                'email' => 'courier@courier.com',
                'password' => password_hash("courier_password", PASSWORD_BCRYPT)
            ]
        ];

        foreach ($users as $key => $user) {
            $uid = $this->db->create('users', $user);
            $this->db->create('users_roles', ['user_id' => $uid, 'role_id' => $roleIds[$key]]);
        }

    }
}
