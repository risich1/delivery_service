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
    }

    public function getByPhone(string $phone): ?User {
        $result = $this->find([
            ['u.phone', '=', $phone]
        ]);

        return array_shift($result);
    }

}
