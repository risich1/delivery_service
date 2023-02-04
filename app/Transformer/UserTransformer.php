<?php

namespace App\Transformer;

use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;

class UserTransformer {

    #[ArrayShape(['id' => "int", 'fullName' => "string"])]
    public function transform(null|User $user): array|null {
        return $user ? [
            'id' => $user->getId(),
            'fullName' => $user->getFullName(),
        ] : null;
    }

}
