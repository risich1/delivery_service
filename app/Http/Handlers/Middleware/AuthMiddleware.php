<?php

namespace App\Http\Handlers\Middleware;

use App\Exceptions\InvalidAuthException;
use App\Http\Request\LoginRequest;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Service\JwtService;
use http\Exception;
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

    protected function authUser() {

    }

}
