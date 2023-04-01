<?php

namespace App;

use App\Http\Request\AuthRequest;
use App\Http\Request\CalculateOrderRequest;
use App\Http\Request\CreateOrderRequest;
use App\Http\Request\HandOrderToCourierRequest;
use App\Http\Request\LoginRequest;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Http\Router\Router;
use App\Interface\IContainer;

class App {

    public function __construct(protected readonly IContainer $container, protected readonly Router $router) {}

    protected function initRoutes() {
        $orderController = $this->container->get('controller.order');
        $authController = $this->container->get('controller.auth');
        $authMiddleware = $this->container->get('middleware.auth');
        $rateLimitMiddleware = $this->container->get('middleware.rate_limit');

        $routes = [
            ['/api/v1/order/$id/courier', 'put', $orderController, 'sendToCourier', [$rateLimitMiddleware, $authMiddleware], HandOrderToCourierRequest::class],
            ['/api/v1/order/calculate', 'post', $orderController, "calculateOrder", [$rateLimitMiddleware, $authMiddleware], CalculateOrderRequest::class],
            ['/api/v1/order', 'get', $orderController, "getAllOrder", [$rateLimitMiddleware, $authMiddleware], AuthRequest::class],
            ['/api/v1/order/$id', 'get', $orderController, 'getOrder', [$rateLimitMiddleware, $authMiddleware], AuthRequest::class],
            ['/api/v1/order', 'post', $orderController, 'createOrder', [$rateLimitMiddleware, $authMiddleware], CreateOrderRequest::class],
            ['/api/v1/login', 'post', $authController, 'login', [], LoginRequest::class]
        ];

        foreach ($routes as $routeParams) {
            [$path, $method, $controller, $handler, $middlewares, $request] = $routeParams;
            $this->router->{$method}($path, fn(...$params) => call_user_func([$controller, $handler], ...$params), $middlewares, $request);
        }

        $migrateHandler = function () {
            (new Migration($this->container->get('db')))->run();
            return new Response('Migrated');
        };

        $seedHandler = function () {
            (new Seeder($this->container->get('db')))->run();
            return new Response('Seeded');
        };

        $this->router->get('/migrate', $migrateHandler, [], Request::class);
        $this->router->get('/seed', $seedHandler, [], Request::class);
    }

    public function boot(): void {
        $this->initRoutes();
        $this->router->run();
    }

}
