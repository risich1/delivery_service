<?php

namespace App\Repository;

use App\Entity\Entity;
use App\Entity\User;
use App\Interface\ISource;

class UserRepository extends Repository {

    protected string $table = 'users';
    protected string $entity = User::class;

    public function __construct(ISource $source) {
        parent::__construct($source, $this->table);

        $this->getQuery = "
            SELECT u.id, u.email, u.password, u.full_name as fullName, GROUP_CONCAT(r.role_name, ',') as roles FROM {$this->table} u 
            JOIN users_roles ur ON ur.user_id = u.id 
            JOIN roles r ON r.id = ur.role_id
        ";
    }

    public function getByEmail(string $email): Entity {
        $result = $this->find([
            ['u.email', '=', $email]
        ]);

//        print_r(array_shift($result));die;

        return $this->dataToEntity(array_shift($result));
    }

    public function getById(string $email): User {
        $result = $this->find([
            ['u.id', '=', $email]
        ]);

        return $this->dataToEntity(array_shift($result));
    }

}
