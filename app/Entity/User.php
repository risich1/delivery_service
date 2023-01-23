<?php

namespace App\Entity;

class User extends Entity {

    protected array $fields = [
        'id', 'full_name', 'email', 'roles'
    ];

}
