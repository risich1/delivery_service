<?php

namespace App\Http\Handlers\Middleware;

use App\Exceptions\RateExceededException;
use App\Interface\IRequest;
use App\Service\HttpService;

class RateLimitMiddleware extends Middleware {

    public function __construct(protected HttpService $http) {}

    /**
     * @throws RateExceededException
     */
    public function process(IRequest $request): IRequest
    {
        $uriParamsStr = implode('_', $request->getUriParams());
        $token = "{$request->getClientIp()}_{$request->getUri()}_{$uriParamsStr}";
        if (!$this->http->isAllowByRateLimit($token)) {
            throw new RateExceededException;
        }

        return parent::process($request);
    }

}
