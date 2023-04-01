<?php

require __DIR__ . '/vendor/autoload.php';

use App\App;
use App\Container;
use App\Http\Router\Router;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

(new App(new Container, new Router))->boot();






