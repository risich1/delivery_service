<?php

namespace App\Entity;

class Product extends Entity {

    protected string $name;
    protected int $sellerId;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getSellerId(): int
    {
        return $this->sellerId;
    }

    /**
     * @param int $sellerId
     */
    public function setSellerId(int $sellerId): void
    {
        $this->sellerId = $sellerId;
    }

}
