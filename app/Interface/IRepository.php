<?php

namespace App\Interface;

use App\Entity\Entity;

interface IRepository {

    public function find(array $conditions): array;

    public function create(Entity $entity): void;

    public function update(array $data, array $conditions): void;

}
