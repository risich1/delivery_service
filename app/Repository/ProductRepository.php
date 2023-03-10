<?php
namespace App\Repository;

use App\Entity\Product;
use App\Interface\ISource;

class ProductRepository extends Repository {

    protected string $table = 'products';
    protected string $entity = Product::class;

    public function __construct(ISource $source) {
        parent::__construct($source, $this->table);
    }

}
