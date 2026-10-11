<?php

use App\Http\Controllers\FrontController;
use Cms\Http\Router;

/** @var Router $router */
$router->get('/', [FrontController::class, 'home']);
$router->get('/blog', [FrontController::class, 'archive']);
$router->get('/{slug}', [FrontController::class, 'show']);
