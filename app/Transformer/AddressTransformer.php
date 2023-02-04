<?php

namespace App\Transformer;

use App\Entity\Address;

class AddressTransformer {

    public function transform(null|Address $address): array|null {
        return $address ? [
            'id' => $address->getId(),
            'title' => $address->getTitle()
        ] : null;
    }

}
