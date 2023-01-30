<?php

namespace App\Http\Handlers\Middleware;

use App\Exceptions\InvalidAuthException;
use App\Service\JwtService;
use App\Interface\IRequest;

class AuthMiddleware extends Middleware {

    /**
     * @throws InvalidAuthException
     */
    public function process(IRequest $request): IRequest
    {
        $user = JwtService::validateJWT($request->getBearerToken());

        if (!$user) {
            throw new InvalidAuthException();
        }

        $request->setUser($user);
        return $request;
    }

}
