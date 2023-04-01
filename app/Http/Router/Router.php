<?php

namespace App\Http\Router;

use App\Http\Handlers\RequestHandler;
use App\Http\Handlers\ResponseHandler;
use App\Http\Request\Request;
use App\Http\Handlers\Middleware\Middleware;
use App\Http\Response\Response;

/**
 * @method get(string $string, \Closure $handlers, array $middlewares,  $request)
 * @method post(string $string, \Closure $handlers, array $middlewares, $request)
 * @method put(string $string, \Closure $handlers, array $middlewares, $request)
 */
class Router {
    private array $handlers;
    private array $middlewares;
    private array $requests;
    private array $httpMethods = [
        'GET', 'PUT', 'POST', 'DELETE'
    ];

    public function __call($name, $args)
    {
        $method = strtoupper($name);
        if (in_array($method, $this->httpMethods)) {
            [$url, $handler, $middlewares, $request] = $args;

            if ($handler instanceof \Closure) {
                $this->handlers["$method::$url"] = $handler;
            }

            foreach ($middlewares ?? [] as $middleware) {
                if ($middleware instanceof Middleware) {
                    $this->middlewares["$method::$url"][] = $middleware;
                }
            }
            $this->requests["$method::$url"] = $request;
        }
    }

    public function run(): void {
        $request = new Request;
        $response = new Response('Method not allowed', [], Response::HTTP_METHOD_NOT_ALLOWED_CODE);
        foreach ($this->handlers as $key => $handler) {
            [$method, $url] = explode('::', $key);
            if ($method !== $request->getMethod()) {
                continue;
            }

            $urlPeaces = explode('/', $url);
            $urlPeaces = array_filter($urlPeaces, fn($p) => $p);
            $rUrl = $request->getUri();
            $rUrlPeaces = explode('/', $rUrl);
            $rUrlPeaces = array_filter($rUrlPeaces, fn($p) => $p);
            $rUrlParams = [];


            $urlParams = [];
            foreach ($urlPeaces as $peaceKey => $peace) {
                if (str_starts_with($peace, '$') && isset($rUrlPeaces[$peaceKey])) {
                    $urlParams[$peaceKey] = $peace;
                    $rUrlParams[] = $rUrlPeaces[$peaceKey];
                }
            }

            if (count($urlParams) && count($rUrlParams) && count($urlPeaces) === count($rUrlPeaces)) {
                $offset = (count($urlPeaces) - count($urlParams)) - 1;
                $rUrl = implode('/', array_slice($rUrlPeaces, $offset));
                $url = implode('/', array_slice($rUrlPeaces, $offset));
            }

            if ($url === $rUrl) {
                $request = new $this->requests[$key];
                $request->setUriParams($rUrlParams);
                $response = (new RequestHandler($handler, $this->middlewares[$key] ?? [], $rUrlParams))->handle($request);
            }
        }

        (new ResponseHandler($response))->handle();
    }
}
