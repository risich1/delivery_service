<?php

namespace App\Repository;

use App\Entity\Entity;
use App\Interface\IRepository;
use App\Interface\ISource;

abstract class Repository implements IRepository {

    protected ISource $source;
    protected string $table;
    protected string $getQuery;
    protected string $entity;

    public function __construct(ISource $source, string $table) {
        $this->table = $table;
        $this->getQuery = "SELECT * FROM {$this->table}";
        $this->source = $source;
    }

    public function create(array $data): void {
        $this->source->create($this->table, $data);
    }

    public function update(array $data, array $conditions): void {
        $this->source->update($this->table, $data, $conditions);
    }

    public function find(array $conditions = []): array {
        $query = $this->getQuery;
        return $this->source->get($query, $conditions);
    }

    protected function dataToEntity(array $data): Entity {
        return new $this->entity($data);
    }

}
