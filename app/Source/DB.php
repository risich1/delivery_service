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

    public function get(string $query, array $conditions = []): array {
        $query = $this->buildQueryWithConditions($query, $conditions);
        $stmt = $this->pdo->query($query);
        $result = [];

        while ($row = $stmt->fetch())
        {
            $result[] = $row;
        }

        return $result;
    }

    protected function buildQueryWithConditions(string $query, array $conditions): string {
        if (count($conditions)) {
            $query .= ' WHERE ';
            foreach ($conditions as $key => $condition) {
                if ($key > 0 && count($conditions) > 1) {
                    $query .= ' AND ';
                }
                if (isset($condition[0], $condition[1], $condition[2])) {
                    $value = "'$condition[2]'";
                    if (is_array($condition[2])) {
                        $valueArray = $condition[2];
                        foreach ($valueArray as &$item) {
                            $item = "'$item'";
                        }
                        $value = '(' . implode(',', $valueArray) . ')';
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
//
//    protected function pdoSetMulti(array $data): string {
//
//        foreach ($data as $key => $dat) {
//           $res = [];
//
//           foreach ($dat as $datKey => $v) {
//              $res["$datKey"]
//           }
//        }
//    }

    public function create(string $table, array $data): int|null {
        $sql = "INSERT INTO $table SET ".$this->pdoSet($data);
//        if (is_array($data[0])) {
//            $sql = "INSERT INTO $table ({implode(',', $data[0])}) VALUES ";
//
//            foreach ($data as $key => $dat) {
//                $iValues = array_values($dat);
//                foreach ($iValues as &$iValue) {
//                    $iValue = "'$iValue'";
//                }
//                $iValues = implode(',', $iValues);
//                $sql .= "( $iValues )";
//
//                if ($key < count($data) - 1) {
//                    $sql .= ', ';
//                }
//            }
//        }

        $stm = $this->pdo->prepare($sql);
        $stm->execute(array_values($data));
        return $this->pdo->lastInsertId();
    }

    public function update(string $table, array $data, array $conditions = []): void {
        $query = "UPDATE $table SET ".$this->pdoSet($data);

        if ($conditions) {
            $query = $this->buildQueryWithConditions($query, $conditions);
        }
        $stm = $this->pdo->prepare($query);
        $stm->execute(array_values($data));
    }

    public function delete(string $table, array $conditions)
    {
        // TODO: Implement delete() method.
    }
}
