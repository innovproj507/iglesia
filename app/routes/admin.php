<?php

use Cms\Admin\Controllers\AuthController;
use Cms\Admin\Controllers\DashboardController;
use Cms\Admin\Controllers\MediaController;
use Cms\Admin\Controllers\MenusController;
use Cms\Admin\Controllers\PluginsController;
use Cms\Admin\Controllers\PostsController;
use Cms\Admin\Controllers\SettingsController;
use Cms\Admin\Controllers\ThemesController;
use Cms\Admin\Controllers\UsersController;
use Cms\Http\Middleware\AuthMiddleware;
use Cms\Http\Middleware\CapabilityMiddleware;
use Cms\Http\Middleware\CsrfMiddleware;
use Cms\Http\Router;

/** @var Router $router */
$router->get('/admin/login', [AuthController::class, 'showLogin']);
$router->post('/admin/login', [AuthController::class, 'login'], [CsrfMiddleware::class]);
$router->post('/admin/logout', [AuthController::class, 'logout'], [CsrfMiddleware::class]);

$router->group('/admin', function (Router $router) {
    $router->get('', [DashboardController::class, 'index']);

    $router->get('/posts', [PostsController::class, 'index']);
    $router->get('/posts/new', [PostsController::class, 'create']);
    $router->post('/posts', [PostsController::class, 'store'], [CsrfMiddleware::class]);
    $router->get('/posts/{id}/edit', [PostsController::class, 'edit']);
    $router->post('/posts/{id}', [PostsController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/posts/{id}/delete', [PostsController::class, 'destroy'], [CsrfMiddleware::class]);

    $router->get('/media', [MediaController::class, 'index'], [[CapabilityMiddleware::class, 'manage_media']]);
    $router->post('/media', [MediaController::class, 'store'], [CsrfMiddleware::class, [CapabilityMiddleware::class, 'manage_media']]);
    $router->post('/media/{id}/delete', [MediaController::class, 'destroy'], [CsrfMiddleware::class, [CapabilityMiddleware::class, 'manage_media']]);

    $router->get('/menus', [MenusController::class, 'index'], [[CapabilityMiddleware::class, 'manage_menus']]);
    $router->post('/menus', [MenusController::class, 'store'], [CsrfMiddleware::class, [CapabilityMiddleware::class, 'manage_menus']]);
    $router->post('/menus/items', [MenusController::class, 'addItem'], [CsrfMiddleware::class, [CapabilityMiddleware::class, 'manage_menus']]);
    $router->post('/menus/items/{id}/delete', [MenusController::class, 'removeItem'], [CsrfMiddleware::class, [CapabilityMiddleware::class, 'manage_menus']]);

    $router->get('/users', [UsersController::class, 'index'], [[CapabilityMiddleware::class, 'manage_users']]);
    $router->post('/users', [UsersController::class, 'store'], [CsrfMiddleware::class, [CapabilityMiddleware::class, 'manage_users']]);
    $router->post('/users/{id}/delete', [UsersController::class, 'destroy'], [CsrfMiddleware::class, [CapabilityMiddleware::class, 'manage_users']]);

    $router->get('/plugins', [PluginsController::class, 'index'], [[CapabilityMiddleware::class, 'manage_plugins']]);
    $router->post('/plugins/{slug}/activate', [PluginsController::class, 'activate'], [CsrfMiddleware::class, [CapabilityMiddleware::class, 'manage_plugins']]);
    $router->post('/plugins/{slug}/deactivate', [PluginsController::class, 'deactivate'], [CsrfMiddleware::class, [CapabilityMiddleware::class, 'manage_plugins']]);

    $router->get('/themes', [ThemesController::class, 'index'], [[CapabilityMiddleware::class, 'manage_themes']]);
    $router->post('/themes/{slug}/activate', [ThemesController::class, 'activate'], [CsrfMiddleware::class, [CapabilityMiddleware::class, 'manage_themes']]);

    $router->get('/settings', [SettingsController::class, 'index'], [[CapabilityMiddleware::class, 'manage_options']]);
    $router->post('/settings', [SettingsController::class, 'update'], [CsrfMiddleware::class, [CapabilityMiddleware::class, 'manage_options']]);
}, [AuthMiddleware::class]);
