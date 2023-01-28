<?php

namespace App\Entity;

use ReflectionClass;

class Entity {

    public function __construct(array $data) {
        $reflection = new ReflectionClass( static::class );
        foreach ($data as $field => $value) {
            foreach ($reflection->getProperties() as $property) {
                if ($property->getName() === $field) {
                    $this->{$field} = $property->getType() != 'array' ? $value : (!is_array($value) ? explode(',', $value) : $value);
                }
            }
        }
    }

}
