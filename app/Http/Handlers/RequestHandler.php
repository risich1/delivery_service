<?php

namespace App\Http\Handlers;

use App\Http\Handlers\Middleware\Middleware;
use App\Http\Response\Response;
use App\Interface\IRequest;

class RequestHandler {

    private array $middleware;
    private $callable;
    private array $params;

    public function __construct(callable $callable, array $middleware = [], array $params = []) {
        $this->middleware = $middleware;
        $this->callable = $callable;
        $this->params = $params;
    }

    public function handle(IRequest $request): Response {
        try {
            $request->validate();
            return ($this->callable)($this->processMiddleware($request), ...$this->params);
        } catch (\Exception $e) {
            $status = in_array($e->getCode(), Response::getStatuses()) ? $e->getCode() : Response::HTTP_SERVER_ERROR_CODE;
            return new Response(['error' => $e->getMessage()], [], $status);
        }
    }

    protected function processMiddleware(IRequest $request): IRequest {
        $chain = $this->buildMiddlewareChain();
        $resultRequest = $request;
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
        foreach ($this->middleware as $middleware) {
            if (!$middlewareChain) {
                $middlewareChain = $middleware;
            } else {
                $middlewareChain->setNext($middleware);
            }
        }

        return $middlewareChain;
    }

}
