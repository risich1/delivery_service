<?php

namespace App\Http\Router;

use App\Http\Handlers\RequestHandler;
use App\Http\Handlers\ResponseHandler;
use App\Http\Request\Request;
use App\Http\Handlers\Middleware\Middleware;
//use App\Http\Handlers\Handler;
use Psr\Http\Message\RequestInterface;

/**
 * @method get(string $string, \Closure $handlers, array $middlewares)
 * @method post(string $string, \Closure $handlers, array $middlewares)
 */
class Router {
    private array $handlers;
    private array $middlewares;
    private Request $request;
    private array $httpMethods = [
        'GET', 'PUT', 'POST', 'DELETE'
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function __call($name, $args)
    {
        $method = strtoupper($name);
        if (in_array($method, $this->httpMethods)) {
            [$url, $handler, $middlewares] = $args;

            if ($handler instanceof \Closure) {
                $this->handlers["$method::$url"] = $handler;
            }

            foreach ($middlewares ?? [] as $middleware) {
                if ($middleware instanceof Middleware) {
                    $this->middlewares["$method::$url"][] = $middleware;
                }
            }
        }
    }

    public function run(): void {
        foreach ($this->handlers as $key => $handler) {
            [$method, $url] = explode('::', $key);
            if ($method !== $this->request->getMethod()) {
                continue;
            }

            $urlPeaces = explode('/', $url);
            $rUrl = $this->request->getUri();
            $rUrlPeaces = explode('/', $rUrl);
            $rUrlParams = [];
            $urlParams = array_filter($urlPeaces, function ($peace) {
                return str_starts_with($peace, '$');
            });

            if (count($urlParams) && count($urlPeaces) === count($rUrlPeaces)) {
                $offset = (count($urlPeaces) - count($urlParams)) - 1;
                $rUrl = implode('/', array_slice($rUrlPeaces, $offset));
                $url = implode('/', array_slice($rUrlPeaces, $offset));
                $rUrlParams = array_slice($rUrlPeaces, (count($rUrlPeaces) - count($urlParams)), count($urlParams));
            }

            if ($url === $rUrl) {
                $response = (new RequestHandler($handler, $this->middlewares[$key] ?? [], $rUrlParams))->handle($this->request);
                (new ResponseHandler($response))->handle();
            }

        }
    }
}
