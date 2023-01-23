<?php

namespace App\Http\Handlers\Middleware;

use App\Http\Request\Request;
use App\Http\Response\Response;

class Middleware {

    private ?Middleware $nextMiddleware;

    public function setNext(Middleware $nextMiddleware): Middleware
    {
        $this->nextMiddleware = $nextMiddleware;
        return $nextMiddleware;
    }

    public function process(Request $request): Request
    {
        if ($this->nextMiddleware instanceof Middleware) {
            return $this->nextMiddleware->process($request);
        }

        return $request;
    }
}
