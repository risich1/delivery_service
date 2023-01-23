<?php

namespace App;

class Migration {

    protected DB $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    function run(): void {

        $queries = [];

        $queries[] = 'CREATE TABLE IF NOT EXISTS users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(30) NOT NULL,
        email VARCHAR(50),
        password VARCHAR(255),
        updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ';

        $queries[] = ' CREATE TABLE IF NOT EXISTS roles (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        role_name VARCHAR(30) NOT NULL,
        updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )';
//
        $queries[] = 'CREATE TABLE IF NOT EXISTS users_roles (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(6) UNSIGNED NOT NULL,
        role_id INT(6) UNSIGNED NOT NULL,
        updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (role_id) REFERENCES roles(id)
        )';
//
//    $query.= 'CREATE TABLE delivery_addresses (
//    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    address VARCHAR(30) NOT NULL,
//    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//    )';
//
//    $query.= 'CREATE TABLE orders (
//    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    customer_id: INT(6) UNSIGNED,
//    seller_id: INT(6) UNSIGNED,
//    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//    )';

        foreach ($queries as $query) {
            $this->db->getInstance()->query($query);
        }

    }

}
