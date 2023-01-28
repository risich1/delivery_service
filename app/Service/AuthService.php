<?php

namespace App\Service;

use App\Exceptions\InvalidAuthException;
use App\Interface\IRepository;
use App\Repository\UserRepository;

class AuthService {

    protected UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws InvalidAuthException
     */
    public function login($email, $password): string {
        $user = $this->repository->getByEmail($email);

        if (!password_verify($password, $user->getPassword())) {
            throw new InvalidAuthException;
        }

        return JwtService::generateJWT($user);
    }

}
