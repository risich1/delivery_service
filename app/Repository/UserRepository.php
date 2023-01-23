<?php

namespace App\Repository;

use App\DB;

class UserRepository extends Repository {

    protected DB $source;
    protected string $table = 'users';

    public function getUser(int $id): array {
        $query = "
    SELECT u.*, GROUP_CONCAT(r.role_name, ',') as roles FROM {$this->table} u 
    JOIN users_roles ur ON ur.user_id = u.id 
    JOIN roles r ON r.id = ur.role_id
    WHERE u.id = $id
    ";

       return $this->source->get($query);
    }

    public function findUserByEmail(string $email): array {
        $query = "
            SELECT u.*, GROUP_CONCAT(r.role_name, ',') as roles FROM {$this->table} u 
            JOIN users_roles ur ON ur.user_id = u.id 
            JOIN roles r ON r.id = ur.role_id
            WHERE u.email = '$email'
        ";

        return $this->source->get($query)[0];
    }

}
