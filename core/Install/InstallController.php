<?php

namespace Cms\Install;

use Cms\Database\Connection;
use Cms\Database\Migrator;
use Cms\Hooks\HookManager;
use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Plugins\PluginManager;
use Cms\Themes\View;
use Composer\Autoload\ClassLoader;
use Database\Seeders\RolesAndOptionsSeeder;
use PDO;
use PDOException;

class InstallController
{
    private const REQUIRED_EXTENSIONS = ['pdo_mysql', 'mbstring', 'json'];

    public function show(Request $request): Response
    {
        if (app()->isInstalled()) {
            return Response::html($this->page('install/done', ['alreadyInstalled' => true]));
        }

        $step = $_SESSION['install_step'] ?? 'requirements';

        return Response::html($this->page('install/' . $step, [
            'requirements' => $this->checkRequirements(),
            'error' => $_SESSION['install_error'] ?? null,
        ]));
    }

    public function advance(Request $request): Response
    {
        $step = (string) $request->param('step');

        if (in_array($step, ['requirements', 'database', 'migrate', 'admin'], true)) {
            $_SESSION['install_step'] = $step;
        }

        return Response::redirect('/install');
    }

    public function testDatabase(Request $request): Response
    {
        $config = [
            'host' => (string) $request->input('host', '127.0.0.1'),
            'port' => (int) $request->input('port', 3306),
            'database' => (string) $request->input('database', ''),
            'username' => (string) $request->input('username', ''),
            'password' => (string) $request->input('password', ''),
        ];

        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['username'], $config['password']);
            $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', $config['database']) . '` CHARACTER SET utf8mb4');
        } catch (PDOException $e) {
            $_SESSION['install_error'] = 'No se pudo conectar: ' . $e->getMessage();

            return Response::redirect('/install');
        }

        $_SESSION['install_db'] = $config;
        $_SESSION['install_step'] = 'migrate';

        return Response::redirect('/install');
    }

    public function migrate(Request $request): Response
    {
        $config = $_SESSION['install_db'] ?? null;

        if (!$config) {
            return Response::redirect('/install');
        }

        $connection = new Connection($config);
        app()->container()->instance(Connection::class, $connection);
        (new Migrator($connection))->run(base_path('database/migrations'), 'core');
        (new RolesAndOptionsSeeder($connection))->run();

        $pluginManager = new PluginManager($connection, new HookManager(), app(ClassLoader::class), base_path('plugins'));
        foreach (array_keys($pluginManager->discover()) as $slug) {
            $pluginManager->install($slug);
        }

        $_SESSION['install_step'] = 'admin';

        return Response::redirect('/install');
    }

    public function createAdmin(Request $request): Response
    {
        $config = $_SESSION['install_db'] ?? null;

        if (!$config) {
            return Response::redirect('/install');
        }

        $connection = new Connection($config);
        $seeder = new RolesAndOptionsSeeder($connection);
        $roleId = $seeder->roleIdFor('admin');

        $connection->insert('users', [
            'uuid' => $this->uuid(),
            'name' => (string) $request->input('name', 'Administrador'),
            'email' => (string) $request->input('email', ''),
            'password_hash' => password_hash((string) $request->input('password', ''), PASSWORD_BCRYPT),
            'role_id' => $roleId,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->writeEnvFile($config);

        unset($_SESSION['install_db'], $_SESSION['install_step'], $_SESSION['install_error']);

        return Response::redirect('/install');
    }

    private function writeEnvFile(array $dbConfig): void
    {
        $appKey = base64_encode(random_bytes(32));

        $env = <<<ENV
        APP_KEY={$appKey}
        APP_URL=http://localhost

        DB_HOST={$dbConfig['host']}
        DB_PORT={$dbConfig['port']}
        DB_DATABASE={$dbConfig['database']}
        DB_USERNAME={$dbConfig['username']}
        DB_PASSWORD={$dbConfig['password']}

        ENV;

        file_put_contents(base_path('.env'), $env);
    }

    private function checkRequirements(): array
    {
        $checks = [
            'PHP >= 8.1' => version_compare(PHP_VERSION, '8.1.0', '>='),
        ];

        foreach (self::REQUIRED_EXTENSIONS as $ext) {
            $checks["Extensión {$ext}"] = extension_loaded($ext);
        }

        $checks['storage/ escribible'] = is_writable(base_path('storage'));
        $checks['public/uploads/ escribible'] = is_writable(base_path('public/uploads'));

        return $checks;
    }

    private function page(string $template, array $data): string
    {
        $engine = new \League\Plates\Engine(base_path('core/Install/Views'));

        return $engine->render($template, $data);
    }

    private function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
