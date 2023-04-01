<?php

namespace App;

use App\Http\Handlers\Controller\AuthController;
use App\Http\Handlers\Controller\OrderController;
use App\Http\Handlers\Middleware\AuthMiddleware;
use App\Http\Handlers\Middleware\RateLimitMiddleware;
use App\Interface\IContainer;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\AuthService;
use App\Service\HttpService;
use App\Service\OrderService;

use App\Transformer\AddressTransformer;
use App\Transformer\OrderTransformer;
use App\Transformer\ProductTransformer;
use App\Transformer\ShortOrderTransformer;
use App\Transformer\UserTransformer;

use App\Source\DB;
use Redis;

class Container implements IContainer
{

    private array $objects;

    public function __construct()
    {
        $db = new DB($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $redis = new Redis;
        $redis->connect($_ENV['REDIS_HOST_NAME']);

        $this->objects = [
            'db' => fn() => $db,
            'redis' => fn() => $redis,
            'repository.user' => fn() => new UserRepository($this->get('db')),
            'repository.product' => fn() => new ProductRepository($this->get('db')),
            'repository.order' => fn() => new OrderRepository($this->get('db')),
            'repository.address' => fn() => new AddressRepository($this->get('db')),
            'service.order' => fn() => new OrderService(
                $this->get('repository.order'),
                $this->get('repository.user'),
                $this->get('repository.product'),
                $this->get('repository.address'),
            ),
            'service.auth' => fn() => new AuthService($this->get('repository.user')),
            'service.http' => fn() => new HttpService($this->get('redis')),
            'transformer.user' => fn() => new UserTransformer,
            'transformer.address' => fn() => new AddressTransformer,
            'transformer.product' => fn() => new ProductTransformer,
            'transformer.order_short' => fn() => new ShortOrderTransformer,
            'transformer.order' => fn() => new OrderTransformer(
                $this->get('transformer.user'),
                $this->get('transformer.address'),
                $this->get('transformer.product'),
                $this->get('service.order'),
            ),
            'middleware.auth' => fn()  => new AuthMiddleware($this->get('service.auth')),
            'middleware.rate_limit' => fn() => new RateLimitMiddleware($this->get('service.http')),
            'controller.order' => fn() => new OrderController(
                $this->get('service.order'),
                $this->get('transformer.order'),
                $this->get('transformer.order_short')
            ),
            'controller.auth' => fn() => new AuthController($this->get('service.auth'))
        ];
    }

    public function has(string $id): bool
    {
        return isset($this->objects[$id]);
    }

    public function get(string $id): mixed
    {
        return $this->objects[$id]();
    }

}
