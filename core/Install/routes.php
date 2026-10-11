<?php

use Cms\Http\Router;
use Cms\Install\InstallController;

/** @var Router $router */
$router->get('/install', [InstallController::class, 'show']);
$router->post('/install/advance/{step}', [InstallController::class, 'advance']);
$router->post('/install/database', [InstallController::class, 'testDatabase']);
$router->post('/install/migrate', [InstallController::class, 'migrate']);
$router->post('/install/admin', [InstallController::class, 'createAdmin']);
