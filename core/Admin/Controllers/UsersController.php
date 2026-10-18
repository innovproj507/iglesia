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
    private const MIN_PASSWORD = 8;

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
            'users.id', 'users.name', 'users.email', 'users.status', 'users.last_login_at', 'roles.name(role_name)',
        ]);

        return $this->render('users/index', [
            'title' => 'Usuarios',
            'users' => $users,
            'roles' => $this->roles(),
            'errors' => $this->pullSession('user_errors') ?? [],
            'old' => $this->pullSession('user_old') ?? [],
            'flash' => $this->pullSession('flash'),
        ], 'users');
    }

    public function store(Request $request): Response
    {
        $data = $this->input($request);
        $password = (string) $request->input('password', '');
        $errors = $this->validate($data, null);

        if (mb_strlen($password) < self::MIN_PASSWORD) {
            $errors['password'] = 'La contraseña debe tener al menos ' . self::MIN_PASSWORD . ' caracteres.';
        }

        if ($errors) {
            $_SESSION['user_errors'] = $errors;
            $_SESSION['user_old'] = $data;

            return Response::redirect('/admin/users');
        }

        $now = date('Y-m-d H:i:s');
        $this->connection->insert('users', $data + [
            'uuid' => $this->uuid(),
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $_SESSION['flash'] = "Usuario {$data['name']} creado.";

        return Response::redirect('/admin/users');
    }

    public function edit(Request $request): Response
    {
        $user = $this->connection->get('users', '*', ['id' => (int) $request->param('id')]);

        if (!$user) {
            return Response::notFound();
        }

        return $this->renderEdit($user, $user, []);
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');
        $user = $this->connection->get('users', '*', ['id' => $id]);

        if (!$user) {
            return Response::notFound();
        }

        $isSelf = $id === $this->auth->user()?->id;
        $data = $this->input($request) + ['status' => $request->input('status') === 'suspended' ? 'suspended' : 'active'];
        $errors = $this->validate($data, $id);

        // Uno mismo no puede quitarse el rol ni suspenderse (evita quedarse fuera del panel)
        if ($isSelf) {
            $data['role_id'] = (int) $user['role_id'];
            $data['status'] = 'active';
        }

        if (!$isSelf && $this->isLastActiveAdmin($user) && ($data['role_id'] !== (int) $user['role_id'] || $data['status'] !== 'active')) {
            $errors['role_id'] = 'Debe quedar al menos un administrador activo.';
        }

        $errors += $this->passwordErrors($request);

        if ($errors) {
            return $this->renderEdit($user, array_merge($user, $data), $errors);
        }

        if ($password = (string) $request->input('password', '')) {
            $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->connection->update('users', $data + ['updated_at' => date('Y-m-d H:i:s')], ['id' => $id]);
        $_SESSION['flash'] = "Usuario {$data['name']} actualizado.";

        return Response::redirect('/admin/users');
    }

    public function destroy(Request $request): Response
    {
        $id = (int) $request->param('id');
        $user = $this->connection->get('users', '*', ['id' => $id]);

        if ($user && $id !== $this->auth->user()?->id && !$this->isLastActiveAdmin($user)) {
            $this->connection->delete('users', ['id' => $id]);
            $_SESSION['flash'] = "Usuario {$user['name']} eliminado.";
        }

        return Response::redirect('/admin/users');
    }

    /** Perfil del usuario que inició sesión. */
    public function profile(Request $request): Response
    {
        $user = $this->connection->get('users', '*', ['id' => $this->auth->user()->id]);

        return $this->render('users/profile', [
            'title' => 'Mi perfil',
            'user' => $user,
            'roleName' => $this->connection->get('roles', 'name', ['id' => $user['role_id']]),
            'errors' => [],
            'flash' => $this->pullSession('flash'),
        ]);
    }

    public function updateProfile(Request $request): Response
    {
        $id = $this->auth->user()->id;
        $user = $this->connection->get('users', '*', ['id' => $id]);
        $data = [
            'name' => trim((string) $request->input('name', '')),
            'email' => mb_strtolower(trim((string) $request->input('email', ''))),
        ];
        $errors = $this->validate($data + ['role_id' => (int) $user['role_id']], $id);
        $password = (string) $request->input('password', '');

        if ($password !== '' && !password_verify((string) $request->input('current_password', ''), $user['password_hash'])) {
            $errors['current_password'] = 'La contraseña actual no es correcta.';
        }

        $errors += $this->passwordErrors($request);

        if ($errors) {
            return $this->render('users/profile', [
                'title' => 'Mi perfil',
                'user' => array_merge($user, $data),
                'roleName' => $this->connection->get('roles', 'name', ['id' => $user['role_id']]),
                'errors' => $errors,
                'flash' => null,
            ]);
        }

        if ($password !== '') {
            $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->connection->update('users', $data + ['updated_at' => date('Y-m-d H:i:s')], ['id' => $id]);
        $_SESSION['flash'] = $password !== '' ? 'Perfil y contraseña actualizados.' : 'Perfil actualizado.';

        return Response::redirect('/admin/profile');
    }

    private function renderEdit(array $user, array $values, array $errors): Response
    {
        return $this->render('users/edit', [
            'title' => 'Editar usuario',
            'user' => $user,
            'values' => $values,
            'roles' => $this->roles(),
            'errors' => $errors,
            'isSelf' => (int) $user['id'] === $this->auth->user()?->id,
        ], 'users');
    }

    private function input(Request $request): array
    {
        return [
            'name' => trim((string) $request->input('name', '')),
            'email' => mb_strtolower(trim((string) $request->input('email', ''))),
            'role_id' => (int) $request->input('role_id'),
        ];
    }

    private function validate(array $data, ?int $ignoreId): array
    {
        $errors = [];

        if ($data['name'] === '') {
            $errors['name'] = 'Escribe el nombre.';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Escribe un correo válido.';
        } elseif ($this->connection->get('users', 'id', array_filter(['email' => $data['email'], 'id[!]' => $ignoreId]))) {
            $errors['email'] = 'Ya existe un usuario con ese correo.';
        }

        if (!$this->connection->get('roles', 'id', ['id' => $data['role_id']])) {
            $errors['role_id'] = 'Elige un rol.';
        }

        return $errors;
    }

    private function passwordErrors(Request $request): array
    {
        $password = (string) $request->input('password', '');

        if ($password === '') {
            return [];
        }

        if (mb_strlen($password) < self::MIN_PASSWORD) {
            return ['password' => 'La contraseña debe tener al menos ' . self::MIN_PASSWORD . ' caracteres.'];
        }

        if ($password !== (string) $request->input('password_confirmation', '')) {
            return ['password_confirmation' => 'Las contraseñas no coinciden.'];
        }

        return [];
    }

    private function isLastActiveAdmin(array $user): bool
    {
        $adminRoleId = (int) $this->connection->get('roles', 'id', ['slug' => 'admin']);

        return (int) $user['role_id'] === $adminRoleId
            && $user['status'] === 'active'
            && $this->connection->count('users', ['role_id' => $adminRoleId, 'status' => 'active']) <= 1;
    }

    private function roles(): array
    {
        return $this->connection->medoo()->select('roles', '*');
    }

    private function pullSession(string $key): mixed
    {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);

        return $value;
    }

    private function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
