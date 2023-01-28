<?php

require __DIR__ . '/vendor/autoload.php';

use App\Entity\User;
use App\Exceptions\InvalidAuthException;
use App\Http\Handlers\Middleware\AuthMiddleware;
use App\Http\Handlers\Middleware\RateLimitMiddleware;
use App\Http\Request\AuthRequest;
use App\Http\Request\LoginRequest;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Http\Router\Router;
use App\Migration;
use App\Repository\UserRepository;
use App\Seeder;
use App\Service\AuthService;
use App\Service\JwtService;
use App\Source\DB;

session_start();

//error_reporting(E_ALL & ~E_WARNING);

//set_error_handler('errHandler', E_ALL);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db = new DB($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
$router = new Router;

$migrateHandler = function () use ($db) {
    (new Migration($db))->run();
    return new Response(['message' => 'success']);
};

$seedHandler = function () use ($db) {
    (new Seeder($db))->run();
    return new Response(['message' => 'success']);
};

$orderHandler = function (AuthRequest $request, $id) {
    return new Response($id);
};

$loginHandler = function (LoginRequest $request) use ($db) {
    $data = $request->getBody();
    $authService = new AuthService(new UserRepository($db));
    return new Response(['token' => $authService->login($data['email'], $data['password'])]);
};

//$router->get('/api/order/$id', $orderHandler, [new RateLimitMiddleware, new AuthMiddleware], new AuthRequest);
$router->get('/api/order/$id', $orderHandler, [new AuthMiddleware], new AuthRequest);
$router->post('/api/login', $loginHandler, [], new LoginRequest);
$router->get('/api/migrate', $migrateHandler, [], new Request);
$router->get('/api/seed', $seedHandler, [], new Request);

//$router->post('/api/order', []);
//$router->post('/api/order/calculate', [], []);
//$router->get('/api/order', []);

//$router->post('/api/register', []);

$router->run();





