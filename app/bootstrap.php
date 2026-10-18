<?php

use Cms\Admin\AdminMenu;
use Cms\Application;
use Cms\Auth\AuthManager;
use Cms\Container;
use Cms\Content\PostRepository;
use Cms\Content\PostTypeRegistry;
use Cms\Content\TaxonomyRegistry;
use Cms\Content\TermRepository;
use Cms\Database\Connection;
use Cms\Hooks\HookManager;
use Cms\Http\Kernel;
use Cms\Http\Router;
use Cms\Media\MediaLibrary;
use Cms\Media\Uploader;
use Cms\Menus\MenuRepository;
use Cms\Plugins\PluginManager;
use Cms\Settings\OptionsRepository;
use Cms\Themes\ThemeManager;
use Cms\Themes\View;
use Composer\Autoload\ClassLoader;

/** @var ClassLoader $loader */
/** @var string $basePath */

$app = Application::create($basePath);
$container = $app->container();
$container->instance(ClassLoader::class, $loader);

$configFile = $basePath . '/config/config.php';
$config = is_file($configFile) ? require $configFile : null;

if ($config) {
    $app->loadConfig($config);
    date_default_timezone_set($config['app']['timezone'] ?? 'UTC');

    $container->singleton(Connection::class, fn () => new Connection($config['db']));
    $container->singleton(OptionsRepository::class, fn (Container $c) => new OptionsRepository($c->make(Connection::class)));
    $container->singleton(AuthManager::class, fn (Container $c) => new AuthManager($c->make(Connection::class)));
    $container->singleton(PostRepository::class, fn (Container $c) => new PostRepository($c->make(Connection::class)));
    $container->singleton(TermRepository::class, fn (Container $c) => new TermRepository($c->make(Connection::class)));
    $container->singleton(TaxonomyRegistry::class, fn () => new TaxonomyRegistry());
    $container->singleton(PostTypeRegistry::class, fn () => new PostTypeRegistry());
    $container->singleton(Uploader::class, fn () => new Uploader($basePath . '/public/uploads'));
    $container->singleton(MediaLibrary::class, fn (Container $c) => new MediaLibrary($c->make(Connection::class), $c->make(Uploader::class)));
    $container->singleton(MenuRepository::class, fn (Container $c) => new MenuRepository($c->make(Connection::class)));
    $container->singleton(AdminMenu::class, fn () => new AdminMenu());
    $container->singleton(PluginManager::class, fn (Container $c) => new PluginManager(
        $c->make(Connection::class),
        $c->make(HookManager::class),
        $c->make(ClassLoader::class),
        $basePath . '/plugins'
    ));
    $container->singleton(ThemeManager::class, fn (Container $c) => new ThemeManager(
        $basePath . '/themes',
        $c->make(OptionsRepository::class)->get('active_theme', 'iglesia-base')
    ));
    $container->singleton(View::class, fn (Container $c) => new View($c->make(ThemeManager::class), $c->make(PluginManager::class)));

    // Plugins register their hooks/routes/post types on every request while active.
    $container->make(PluginManager::class)->bootActive();
    $container->make(PostTypeRegistry::class)->boot();
}

$router = new Router();
$container->instance(Router::class, $router);

// Order matters for FastRoute: static/specific routes must be registered
// before the public catch-all "/{slug}" route in web.php.
require $basePath . '/core/Install/routes.php';
require $basePath . '/app/routes/admin.php';

if ($config) {
    do_action('router.routes', $router);
}

// The public catch-all "/{slug}" route must be registered last.
require $basePath . '/app/routes/web.php';

return new Kernel($app, $router);
