<?php

namespace App\Http\Handlers\Middleware;

use App\Http\Request\LoginRequest;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\RateLimiter;
use App\Service\JwtService;
use http\Exception;
use App\Interface\IRequest;

class RateLimitMiddleware extends Middleware {

    public function process(IRequest $request): IRequest
    {
        $rateLimiter = new RateLimiter($_SERVER["REMOTE_ADDR"]);
        $rateLimiter->limitRequestsInMinutes(10, 1);
        return $request;
    }

}
