<?php

namespace App\Service;

use App\Exceptions\InvalidAuthException;
use App\Repository\UserRepository;

class AuthService {

    protected UserRepository $repository;

    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * @throws InvalidAuthException
     */
    public function login(string $phone, string $password): string {
        $user = $this->repository->getByPhone($phone);

        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new InvalidAuthException;
        }

        return JwtService::generateJWT($user);
    }

}
