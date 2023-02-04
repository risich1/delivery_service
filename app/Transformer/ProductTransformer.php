<?php

namespace App\Transformer;

class ProductTransformer {

    public function transform(array $products): array {
        foreach ($products as &$product) {
            $product = [
                'id' => $product->getId(),
                'name' => $product->getName()
            ];
        }
        return $products;
    }

}
