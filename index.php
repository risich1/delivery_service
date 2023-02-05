<?php

require __DIR__ . '/vendor/autoload.php';

use App\Http\Request\AuthRequest;
use App\Http\Request\CalculateOrderRequest;
use App\Http\Request\CreateOrderRequest;
use App\Http\Request\LoginRequest;
use App\Http\Request\Request;
use App\Http\Request\HandOrderToCourierRequest;
use App\Http\Response\Response;
use App\Http\Router\Router;
use App\Migration;
use App\Seeder;
use App\Container;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$container = new Container;

$orderService = $container->get('service.order');
$authService = $container->get('service.auth');
$userTransformer = $container->get('transformer.user');
$addressTransformer = $container->get('transformer.address');
$productTransformer = $container->get('transformer.product');
$orderTransformer = $container->get('transformer.order');
$shortOrderTransformer = $container->get('transformer.order_short');
$authMiddleware = $container->get('middleware.auth');
$rateLimitMiddleware = $container->get('middleware.rate_limit');

$router = new Router;

$loginHandler = function (LoginRequest $request) use ($authService) {
    $body = $request->getBody();
    return new Response(['token' => $authService->login($body['phone'], $body['password'])]);
};

$getOrderHandler = function (AuthRequest $request, $id) use ($orderService, $orderTransformer) {
    $order = $orderService->getOrderById($id, $request->getUser());
    return new Response($orderTransformer->transform($order));
};

$getAllOrderHandler = function (AuthRequest $request) use ($orderService, $shortOrderTransformer) {
    $result = $orderService->getOrderList($request->getUser());
    return new Response($shortOrderTransformer->transform($result));
};

$createOrderHandler = function (CreateOrderRequest $request) use ($orderService) {
    $orderService->createOrder($request->getBody());
    return new Response('Order created', [], Response::HTTP_CREATED_CODE);
};

$calculateOrderHandler = function (CalculateOrderRequest $request) use ($orderService) {
    $body = $request->getBody();
    return new Response(['cost' => $orderService->calculateCost($request->getUser(), $body['address_a_id'], $body['address_b_id'])]);
};

$migrateHandler = function () use ($container) {
    (new Migration($container->get('db')))->run();
    return new Response('Migrated');
};

$seedHandler = function () use ($container) {
    (new Seeder($container->get('db')))->run();
    return new Response('Seeded');
};

$sendToCourierHandler = function (HandOrderToCourierRequest $request, int $id) use ($orderService) {
    $orderService->handOrderToCourier($id, $request->getUser(), $request->getBody()['courier_id']);
    return new Response('Order has been handed');
};

$router->put('/api/v1/order/$id/courier', $sendToCourierHandler, [$rateLimitMiddleware, $authMiddleware], new HandOrderToCourierRequest);
$router->post('/api/v1/order/calculate', $calculateOrderHandler,  [$rateLimitMiddleware, $authMiddleware], new CalculateOrderRequest);
$router->get('/api/v1/order', $getAllOrderHandler, [$rateLimitMiddleware, $authMiddleware], new AuthRequest);
$router->get('/api/v1/order/$id', $getOrderHandler, [$rateLimitMiddleware, $authMiddleware], new AuthRequest);
$router->post('/api/v1/order', $createOrderHandler, [$rateLimitMiddleware, $authMiddleware], new CreateOrderRequest);
$router->post('/api/v1/login', $loginHandler, [$rateLimitMiddleware], new LoginRequest);

$router->get('/migrate', $migrateHandler, [], new Request);
$router->get('/seed', $seedHandler, [], new Request);

$router->run();





