<?php

namespace App\Http\Handlers\Middleware;

use App\Exceptions\InvalidAuthException;
use App\Http\Request\AuthRequest;
use App\Service\AuthService;
use App\Interface\IRequest;

class AuthMiddleware extends Middleware {

    protected AuthService $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @throws InvalidAuthException
     * @param $request AuthRequest
     */
    public function process(IRequest $request): IRequest
    {
        $user = $this->auth->checkJwtAuth($request->getBearerToken());
        if (!$user) {
            throw new InvalidAuthException();
        }
        $request->setUser($user);
        return parent::process($request);
    }

}
