<?php

namespace App\Entity;

use ReflectionClass;

class Entity {

    protected int $id;

    public function __construct(array $data) {
        $this->setId($data['id'] ?? 0);
        $reflection = new ReflectionClass( static::class );
        foreach ($data as $field => $value) {
            $field = lcfirst(str_replace('_', '', ucwords($field, '_')));
            foreach ($reflection->getProperties() as $property) {
                if ($property->getName() === $field) {
                    if ($property->getType() == 'array' && !is_array($value)) {
                        $value = explode(',', $value);
                    }
                    $setter = 'set' . ucfirst($field);
                    $this->{$setter}($value);
                }
            }
        }
    }

    public function toArray(): array {
        $reflection = new ReflectionClass(static::class);

        $result = [];
        foreach ($reflection->getProperties() as $property) {
            $getter = 'get' . ucfirst($property->getName());
            $pName = preg_replace("/[A-Z]/", '_' . "$0", lcfirst($property->getName()));
            $pValue = $this->$getter();
            $result[strtolower($pName)] = $pValue instanceof Entity ? $pValue->toArray() : $pValue;
        }

        if (isset($result['id']) && !$result['id']) {
            unset($result['id']);
        }

        return $result;

    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

}
