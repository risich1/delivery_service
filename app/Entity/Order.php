<?php

namespace App\Entity;

class Order extends Entity {

    const STATUS_PENDING = 'pending',
          STATUS_APPROVED = 'approved',
          STATUS_SUCCESS = 'success';

    protected int $customerId;
    protected int $sellerId;
    protected ?int $courierId = null;
    protected int $addressAId;
    protected int $addressBId;
    protected string $status = self::STATUS_PENDING;
    protected array $products;
    protected int $cost;

    /**
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }

    /**
     * @param int $cost
     */
    public function setCost(int $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param array $products
     */
    public function setProducts(array $products): void
    {
        $this->products = $products;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     */
    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
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

    /**
     * @return int
     */
    public function getCourierId(): ?int
    {
        return $this->courierId;
    }

    /**
     * @param ?int $courierId
     */
    public function setCourierId(?int $courierId): void
    {
        $this->courierId = $courierId;
    }

    /**
     * @return int
     */
    public function getAddressAId(): int
    {
        return $this->addressAId;
    }

    /**
     * @param int $addressAId
     */
    public function setAddressAId(int $addressAId): void
    {
        $this->addressAId = $addressAId;
    }

    /**
     * @return int
     */
    public function getAddressBId(): int
    {
        return $this->addressBId;
    }

    /**
     * @param int $addressBId
     */
    public function setAddressBId(int $addressBId): void
    {
        $this->addressBId = $addressBId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

}
