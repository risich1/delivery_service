<?php

namespace App\Source;

use App\Interface\ISource;
use PDO;

class DB implements ISource
{

    private PDO $pdo;

    public function __construct(string $host, string $dbName, string $user, string $password) {
        $dsn = "mysql:host=$host;dbname=$dbName;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->pdo = new PDO($dsn, $user, $password, $opt);
    }

    public function getInstance(): PDO
    {
        return $this->pdo;
    }

    public function query(string $query): string|false {
        return $this->pdo->query($query);
    }

    public function get(string $query, array $conditions = [], string $groupBy = ''): array {
        $query = $this->buildQueryWithConditions($query, $conditions);
        if ($groupBy) {
            $query .= "GROUP BY $groupBy";
        }
        return $this->pdo->query($query)->fetchAll() ?? [];
    }

    protected function buildQueryWithConditions(string $query, array $conditions): string {
        if (count($conditions)) {
            $query .= ' WHERE ';
            foreach ($conditions as $key => $condition) {
                if ($key > 0 && count($conditions) > 1) {
                    $query .= ' AND ';
                }
                if (isset($condition[0], $condition[1], $condition[2])) {
                    if (is_array($condition[2])) {
                        $valueArray = $condition[2];
                        foreach ($valueArray as $key2 => &$item) {
                            $item = "'$item'";
                        }
                        $value = '(' . implode(',', $valueArray) . ')';
                    } else {
                        $value = "'$condition[2]'";
                    }
                    $query .= "$condition[0] $condition[1] $value";
                }
            }
        }

        return $query;
    }

    protected function pdoSet(array $data): string {
        $set = '';
        $fields = array_keys($data);
        foreach ($fields as $field) {
            $set.="`".str_replace("`","``",$field)."`". "=:$field, ";
        }
        return substr($set, 0, -2);
    }

    public function createBatch(string $table, array $data): int {
        $columns = implode(',', array_keys($data[0]));
        $place_holder = '(' . implode(',', array_fill(0, count($data[0]), '?')) . ')';
        $place_holders = implode(',', array_fill(0, count($data), $place_holder));
        $flat = call_user_func_array('array_merge', array_map('array_values', $data));
        $this->pdo->beginTransaction();
        $stm = $this->pdo->prepare("INSERT INTO {$table} ({$columns}) VALUES {$place_holders}");
        $stm->execute($flat);
        $insertId = $this->pdo->lastInsertId();
        $this->pdo->commit();
        return $insertId;
    }

    public function create(string $table, array $data): int {
        return $this->createBatch($table, [$data]);
    }

    public function update(string $table, array $data, array $conditions = []): void {
        $query = "UPDATE $table SET ". $this->pdoSet($data);

        if ($conditions) {
            $query = $this->buildQueryWithConditions($query, $conditions);
        }

        $this->pdo->beginTransaction();
        $stm = $this->pdo->prepare($query);
        $stm->execute(array_values($data));
        $this->pdo->commit();
    }

    public function delete(string $table, array $conditions)
    {
        // TODO: Implement delete() method.
    }
}
