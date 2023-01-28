<?php

namespace App\Interface;

interface IRepository {

    public function find(array $conditions): array;

    public function create(array $data): void;

    public function update(array $data, array $conditions): void;

}
