<?php

namespace App\Entity;

use ReflectionClass;

class User extends Entity {

    public const CUSTOMER_ROLE = 'customer',
                 SELLER_ROLE = 'seller',
                 COURIER_ROLE = 'courier';

    protected string $fullName;
    protected string $phone;
    protected string $password;
    protected string $uRole;

    /**
     * @return string
     */
    public function getUrole(): string
    {
        return $this->uRole;
    }

    /**
     * @param string $role
     */
    public function setUrole(string $role): void
    {
        $this->uRole = $role;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }


}
