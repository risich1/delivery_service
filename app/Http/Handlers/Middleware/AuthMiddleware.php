<?php

namespace App\Http\Handlers\Middleware;

use App\Http\Request\AuthRequest;
use App\Http\Request\Request;
use App\Http\Response\Response;

class AuthMiddleware extends Middleware {

    public function process(Request $request): Request
    {
        $request->setUser(['uname' => 'name123']);
        return $request;
    }

    protected function authUser() {

    }

}
