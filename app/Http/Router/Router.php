<?php

namespace App\Http\Router;

use App\Http\Handlers\RequestHandler;
use App\Http\Handlers\ResponseHandler;
use App\Http\Request\Request;
use App\Http\Handlers\Middleware\Middleware;
//use App\Http\Handlers\Handler;
use App\Http\Response\Response;
use Psr\Http\Message\RequestInterface;

/**
 * @method get(string $string, \Closure $handlers, array $middlewares,  $request)
 * @method post(string $string, \Closure $handlers, array $middlewares, $request)
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

            $this->requests["$method::$url"] = $request instanceof Request ? $request : new Request;

        }
    }

    public function run(): void {
        $request = new Request;
        foreach ($this->handlers as $key => $handler) {
            [$method, $url] = explode('::', $key);
            if ($method !== $request->getMethod()) {
                continue;
            }

            $urlPeaces = explode('/', $url);
            $rUrl = $request->getUri();
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
                $response = (new RequestHandler($handler, $this->middlewares[$key] ?? [], $rUrlParams))->handle($this->requests[$key]);
                (new ResponseHandler($response))->handle();
            }

        }
    }
}
