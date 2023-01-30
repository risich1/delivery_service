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
        $tablePrefix = mb_substr($table, 0, 1);
        $this->getQuery = "SELECT * FROM {$this->table} {$tablePrefix}";
        $this->source = $source;
    }

    public function create(Entity $entity): void {
        $this->source->create($this->table, $entity->toArray());
    }

    public function update(array $data, array $conditions): void {
        $this->source->update($this->table, $data, $conditions);
    }

    public function find(array $conditions = []): array {
        $query = $this->getQuery;
        $result = $this->source->get($query, $conditions);

        foreach ($result as &$item) {
            if (!$item['id']) {
                return [];
            }
            $item = $this->dataToEntity($item);
        }

        return $result;
    }

    protected function dataToEntity(array $data): Entity {
        return new $this->entity($data);
    }

}
