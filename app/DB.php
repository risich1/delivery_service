<?php

namespace App;

use PDO, PDOStatement;

class DB
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

    public function get(string $query): array {
        $stmt = $this->pdo->query($query);
        $result = [];

        while ($row = $stmt->fetch())
        {
            $result[] = $row;
        }

        return $result;
    }

    protected function pdoSet(array $data): string {
        $set = '';
        $fields = array_keys($data);
        foreach ($fields as $field) {
            $set.="`".str_replace("`","``",$field)."`". "=:$field, ";
        }
        return substr($set, 0, -2);
    }

    public function create(string $table, array $data): int|null {
        $sql = "INSERT INTO $table SET ".$this->pdoSet($data);
        $stm = $this->pdo->prepare($sql);
        $stm->execute(array_values($data));
        return $this->pdo->lastInsertId ();
    }

    public function update(string $table, array $data, string $conditions = ''): void {
        $query = "UPDATE $table SET ".$this->pdoSet($data);
        if ($conditions) {
            $query .= " WHERE $conditions";
        }
        $stm = $this->pdo->prepare($query);
        $stm->execute(array_values($data));
    }

}
