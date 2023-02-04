<?php

namespace App\Repository;

use App\Entity\Entity;
use App\Interface\IEntity;
use App\Interface\IRepository;
use App\Interface\ISource;

abstract class Repository implements IRepository {

    protected ISource $source;
    protected string $table;
    protected string $getQuery;
    protected string $groupBy;
    protected string $entity;
    protected string $tablePrefix;

    public function __construct(ISource $source, string $table) {
        $this->table = $table;
        $this->tablePrefix  = mb_substr($table, 0, 1);
        $this->getQuery = "SELECT * FROM {$this->table} {$this->tablePrefix}";
        $this->groupBy = "{$this->tablePrefix}.id";
        $this->source = $source;
    }

    public function create(IEntity $entity): void {
        $this->source->create($this->table, $entity->toArray());
    }

    public function update(array $data, array $conditions): void {
        $this->source->update($this->table, $data, $conditions);
    }

    public function updateOne(int $id, array $data) {
        $this->update($data, [[ "id", '=',$id]]);
    }

    public function find(array $conditions = []): array {
        $query = $this->getQuery;
        $result = $this->source->get($query, $conditions, $this->groupBy);

        foreach ($result as &$item) {
            if (!$item['id']) {
                return [];
            }
            $item = $this->dataToEntity($item);
        }

        return $result;
    }

    public function getByIds(array $ids): array
    {
        return $this->find([
            [ "{$this->tablePrefix}.id", 'IN', $ids]
        ]);
    }

    public function getById(int $id): IEntity|null {
        $result = $this->getByIds([$id]);
        return $result ? array_shift($result) : null;
    }

    protected function dataToEntity(array $data): IEntity {
        return new $this->entity($data);
    }

}
