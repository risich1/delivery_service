<?php

namespace App\Http\Request;

use App\Entity\User;

class AuthRequest extends Request {

    protected User $user;
    protected array $requireBodyFields = [];
    protected array $requireHeaders = ['Authorization'];

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): self {
        $this->user = $user;
        return $this;
    }

    public function getBearerToken(): string {
        return explode('Bearer ', $this->getHeader('Authorization'))[1];
    }

}
