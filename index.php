<?php

require __DIR__ . '/vendor/autoload.php';

use App\Http\Handlers\Middleware\AuthMiddleware;
use App\Http\Response\Response;
use App\Http\Router\Router;
use App\Repository\UserRepository;
use App\Http\Request\Request;
use App\DB;
use App\Migration;
use App\Seeder;
use App\Service\JwtService;
use App\Entity\User;

//error_reporting(E_ALL & ~E_WARNING);

function errHandler () {
    Response::json(['message' => 'some error'], 500);
    exit;
};

//set_error_handler('errHandler', E_ALL);


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db = new DB($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
$router = new Router(new Request());

$loginHandler = function ( Request $request) use ($db) {
    $data = $request->getBody();
    $user = (new UserRepository($db))->findUserByEmail($data['email']);

    if (!$user || !password_verify($data['password'], $user['password'])) {
        Response::json(['message' => 'Invalid login or password'], 401);
    }

    Response::json(['token' => JwtService::generateJWT(new User($user))]);

};

$authMiddleware = function () {

    $headers = getallheaders();
    $jwt = explode('Bearer ', $headers['Authorization'] ?? '')[1];

    try {
        if (!JwtService::validateJWT($jwt)) {
            Response::json(['message' => 'Unauthorized'], 401);
        }
    } catch (Exception $e) {
        Response::json(['message' => 'Unauthorized'], 401);
    }

};

$migrateHandler = function () use ($db) {
    (new Migration($db))->run();
    Response::json(['message' => 'success']);
};

$seedHandler = function () use ($db) {
    (new Seeder($db))->run();
    Response::json(['message' => 'success']);
};

//$router->get('/api/migrate', $migrateHandler, []);
//$router->get('/api/seed', $seedHandler, []);
//$router->post('/api/order', []);
//$router->post('/api/order/calculate', [], []);
//$router->get('/api/order', []);

$orderHandler = function (Request $request, $id) {
    return new Response($request->getUser());
};

$router->get('/api/order/$id', $orderHandler, [new AuthMiddleware]);

//$router->post('/api/login', $loginHandler, []);

//$router->post('/api/register', []);

$router->run();





