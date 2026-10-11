<?php

namespace Cms\Admin\Controllers;

use Cms\Admin\AdminMenu;
use Cms\Auth\AuthManager;
use Cms\Database\Connection;
use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Themes\View;

class UsersController extends AdminController
{
    public function __construct(
        View $view,
        AuthManager $auth,
        AdminMenu $adminMenu,
        private Connection $connection,
    ) {
        parent::__construct($view, $auth, $adminMenu);
    }

    public function index(Request $request): Response
    {
        $users = $this->connection->medoo()->select('users', ['[><]roles' => ['role_id' => 'id']], [
            'users.id', 'users.name', 'users.email', 'users.status', 'roles.name(role_name)',
        ]);

        $roles = $this->connection->medoo()->select('roles', '*');

        return $this->render('users/index', [
            'title' => 'Usuarios',
            'users' => $users,
            'roles' => $roles,
        ], 'users');
    }

    public function store(Request $request): Response
    {
        $this->connection->insert('users', [
            'uuid' => $this->uuid(),
            'name' => (string) $request->input('name', ''),
            'email' => (string) $request->input('email', ''),
            'password_hash' => password_hash((string) $request->input('password', ''), PASSWORD_BCRYPT),
            'role_id' => (int) $request->input('role_id'),
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return Response::redirect('/admin/users');
    }

    public function destroy(Request $request): Response
    {
        $this->connection->delete('users', ['id' => (int) $request->param('id')]);

        return Response::redirect('/admin/users');
    }

    private function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
