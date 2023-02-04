<?php

namespace App\Service;

use App\Entity\User;
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

    public function checkJwtAuth(string $token): bool|User {
        $validationResult = JwtService::validateJWT($token);
        if (!(int) $validationResult['id']) {
            return false;
        }

        $user = $this->repository->getById($validationResult['id']);
        if (is_null($user)) {
            return false;
        }

        return $user;
    }

}
