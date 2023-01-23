<?php

namespace App\Http\Handlers;

use App\Http\Handlers\Middleware\Middleware;
use App\Http\Request\Request;
use App\Http\Response\Response;

class RequestHandler {

    private array $middleware = [];
    private $callable;
    private array $params;

    public function __construct(callable $callable, array $middleware = [], array $params = []) {
        $this->middleware = $middleware;
        $this->callable = $callable;
        $this->params = $params;
    }

    public function handle(Request $request): Response {
        return ($this->callable)($this->processMiddleware($request), ...$this->params);
    }

    protected function processMiddleware(Request $request): null|Response|Request {
        $chain = $this->buildMiddlewareChain();
        $resultRequest = null;
        if ($chain instanceof Middleware) {
            $resultRequest = $chain->process($request);
        }

        return $resultRequest;
    }

    protected function buildMiddlewareChain(): bool|Middleware {
        if (!$this->middleware) {
            return false;
        }

        $middlewareChain = false;
        foreach ($this->middleware as $index => $middleware) {
            if ($index === 0) {
                $middlewareChain = $middleware;
            } else {
                $middlewareChain->setNext($middleware);
            }
        }

        return $middlewareChain;
    }

}
