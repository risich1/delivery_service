<?php

namespace App\Service;

use App\Entity\User;
use App\Exceptions\InvalidAuthException;
use App\Repository\UserRepository;

class AuthService {

    public function __construct(protected UserRepository $repository) {}

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

    /**
     * @throws InvalidAuthException
     */
    public function checkJwtAuth(string $token): bool|User {
        try {
            $validationResult = JwtService::validateJWT($token);
        } catch (\Exception $e) {
            throw new InvalidAuthException($e->getMessage());
        }

        if (!(int) $validationResult['id']) {
            return false;
        }

        $user = $this->repository->getById($validationResult['id']);

        return is_null($user) ? false : $user;
    }

}
