<?php

namespace App\Service;

use DateTimeImmutable;
use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService {

    public static function generateJWT(User $user): string {
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+60 minutes');

        $data = [
            'iat'  => $issuedAt->getTimestamp(),
            'iss'  => $_ENV['PROJECT_NAME'],
            'nbf'  => $issuedAt->getTimestamp(),
            'exp'  => $expire->getTimestamp(),
            'user' => [
                'id' => $user->getId(),
                'phone' => $user->getPhone(),
                'u_role' => $user->getUrole()
            ]
        ];

        return JWT::encode($data, $_ENV['JWT_SECRET'], 'HS256');
    }

    public static function validateJWT(string $jwt): bool|User {
        $key = new Key($_ENV['JWT_SECRET'], 'HS256');
        $token = JWT::decode($jwt, $key);
        $now = new DateTimeImmutable();
        $nowT = $now->getTimestamp();
        $invalidToken = $token->iss != $_ENV['PROJECT_NAME'] || $token->nbf > $nowT || $token->exp < $nowT;

        return !$invalidToken ? new User((array) $token->user): false;
    }

}
