<?php

namespace App\Repository;

use App\DB;

class Repository  {

    protected DB $source;
    protected string $table;

    public function __construct(DB $source) {
        $this->source = $source;
    }

    public function create(array $data): void {
        $this->source->create($this->table, $data);
    }

    public function update(array $data, string $conditions = ''): void {
        $this->source->update($this->table, $data, $conditions);
    }

}
