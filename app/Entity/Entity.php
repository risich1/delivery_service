<?php

namespace App\Entity;

class Entity {

    protected array $fields = [];

    public function __get(string $name)
    {
        if (in_array($name, array_keys($this->fields))) {
            return $this->fields[$name];
        }
    }

    public function __set(string $name, $value): void
    {
        if (in_array($name, array_keys($this->fields))) {
            $this->fields[$name] = $value;
        }
    }

    public function __construct($data)
    {
        foreach ($data as $field => $value) {
            if (in_array($field, array_keys($this->fields))) {
                $this->fields[$field] = $value;
            }
        }
    }
}
