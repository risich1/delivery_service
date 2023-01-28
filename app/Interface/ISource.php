<?php

namespace App\Interface;

interface ISource {

    public function get(string $query, array $conditions = []): array;

    public function query(string $query);

    public function update(string $table, array $data, array $conditions = []);

    public function create(string $table, array $data);

    public function delete(string $table, array $conditions);

}
