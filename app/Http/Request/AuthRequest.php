<?php

namespace App\Http\Request;

use App\Entity\User;

class AuthRequest extends Request {
    protected array $user;

    public function getUser(): array {
        return $this->user;
    }

    public function setUser(array $user): self {
        $this->user = $user;
        return $this;
    }
}
