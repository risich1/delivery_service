<?php

require __DIR__ . '/vendor/autoload.php';

use App\Http\Handlers\Middleware\AuthMiddleware;
use App\Http\Handlers\Middleware\RateLimitMiddleware;
use App\Http\Request\AuthRequest;
use App\Http\Request\CalculateOrderRequest;
use App\Http\Request\CreateOrderRequest;
use App\Http\Request\LoginRequest;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Http\Router\Router;
use App\Migration;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Seeder;
use App\Service\AuthService;
use App\Service\OrderService;
use App\Source\DB;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db = new DB($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

$orderRepository = new OrderRepository($db);
$userRepository = new UserRepository($db);
$orderService = new OrderService($orderRepository);
$authService = new AuthService($userRepository);

$router = new Router;

$seedHandler = function () use ($db) {
    (new Seeder($db))->run();
    return new Response(['message' => 'success']);
};

$loginHandler = function (LoginRequest $request) use ($authService) {
    return new Response(['token' => $authService->login(...$request->getBody())]);
};

$getOrderHandler = function (AuthRequest $request, $id) use ($orderService) {
    $order = $orderService->getOrderById($id, $request->getUser());
    return new Response(['order' => $order->toArray()]);
};

$getAllOrderHandler = function (AuthRequest $request) use ($orderService) {
    return new Response($orderService->getOrderList($request->getUser()));
};

$createOrderHandler = function (CreateOrderRequest $request) use ($orderService) {
    $orderService->createOrder($request->getBody());
    return new Response(['message' => 'created'], [], 201);
};

$calculateOrderHandler = function (CalculateOrderRequest $request) use ($orderService) {
    return new Response(['cost' => $orderService->calculateCost(...$request->getBody())]);
};

$updateOrderHandler = function (AuthRequest $request, $id) {
    return new Response($id);
};

$migrateHandler = function (Request $request) use ($db) {
    (new Migration($db))->run();
    return new Response(['message' => 'success']);
};

$router->get('/api/migrate', $migrateHandler, [], new Request);
$router->get('/api/seed', $seedHandler, [], new Request);
$router->post('/api/order/calculate', $calculateOrderHandler,  [new AuthMiddleware], new AuthRequest);

$router->get('/api/order', $getAllOrderHandler, [new AuthMiddleware], new AuthRequest);
$router->get('/api/order/$id', $getOrderHandler, [new AuthMiddleware], new AuthRequest);
$router->post('/api/order', $createOrderHandler, [new AuthMiddleware], new CreateOrderRequest);
$router->put('/api/order/$id', $updateOrderHandler, [new AuthMiddleware], new AuthRequest);

$router->post('/api/login', $loginHandler, [], new LoginRequest);


//$router->get('/api/order/$id', $orderHandler, [new RateLimitMiddleware, new AuthMiddleware], new AuthRequest);


$router->run();





