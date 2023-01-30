<?php
namespace App\Repository;

use App\Entity\Address;
use App\Entity\Product;
use App\Interface\ISource;

class AddressRepository extends Repository {

    protected string $table = 'addresses';
    protected string $entity = Address::class;

    public function __construct(ISource $source) {
        parent::__construct($source, $this->table);
    }

}
