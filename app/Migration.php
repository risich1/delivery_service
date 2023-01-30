<?php

namespace App;

use App\Interface\ISource;

class Migration {

    protected ISource $db;

    public function __construct(ISource $db)
    {
        $this->db = $db;
    }

    function run(): void {
        $queries = [];

        $queries[] = 'CREATE TABLE IF NOT EXISTS addresses (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        lng DECIMAL(5,2) NOT NULL, 
        lat DECIMAL(4,2) NOT NULL, 
        updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )';

        $queries[] = "CREATE TABLE IF NOT EXISTS users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        u_role ENUM('customer','seller','courier') DEFAULT 'customer' NOT NULL,
        full_name VARCHAR(30) NOT NULL,
        phone VARCHAR(20) UNIQUE NOT NULL,
        default_address_id INT(6) UNSIGNED,
        password VARCHAR(255) NOT NULL,
        updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (default_address_id) REFERENCES addresses(id)
        )";

        $queries[] = 'CREATE TABLE IF NOT EXISTS users_addresses (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(6) UNSIGNED NOT NULL,
        address_id INT(6) UNSIGNED NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (address_id) REFERENCES addresses(id),
        updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ';

        $queries[] = "CREATE TABLE IF NOT EXISTS orders (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_id INT(6) UNSIGNED NOT NULL,
        seller_id INT(6) UNSIGNED NOT NULL,
        courier_id INT(6) UNSIGNED,
        address_a_id INT(6) UNSIGNED NOT NULL,
        address_b_id INT(6) UNSIGNED NOT NULL,
        cost FLOAT(6) UNSIGNED,
        status ENUM('approved','pending','success') DEFAULT 'pending' NOT NULL,
        updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES users(id),
        FOREIGN KEY (seller_id) REFERENCES users(id),
        FOREIGN KEY (courier_id) REFERENCES users(id),
        FOREIGN KEY (address_a_id) REFERENCES addresses(id),
        FOREIGN KEY (address_b_id) REFERENCES addresses(id)
        )";

        $queries[] = 'CREATE TABLE IF NOT EXISTS products (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        seller_id INT(6) UNSIGNED NOT NULL,
        name VARCHAR(100) NOT NULL,
        updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (seller_id) REFERENCES users(id)
        )';

        $queries[] = 'CREATE TABLE IF NOT EXISTS orders_products (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        order_id INT(6) UNSIGNED NOT NULL,
        product_id INT(6) UNSIGNED NOT NULL,
        updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
        )';

        foreach ($queries as $query) {
            $this->db->getInstance()->query($query);
        }
    }

}
