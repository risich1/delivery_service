<?php

namespace App\Http\Handlers\Controller;

use App\Http\Request\LoginRequest;
use App\Http\Response\Response;
use App\Service\AuthService;

class AuthController extends Controller {

    public function __construct(protected readonly AuthService $authService) {}

    public function login(LoginRequest $request): Response {
        $body = $request->getBody();
        return new Response(['token' => $this->authService->login($body['phone'], $body['password'])]);
    }

}
