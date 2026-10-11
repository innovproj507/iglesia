<?php

namespace Cms\Auth;

use Cms\Database\Connection;

class AuthManager
{
    private ?User $resolvedUser = null;
    private bool $attemptedResolve = false;

    public function __construct(private Connection $connection)
    {
    }

    public function attempt(string $email, string $password): bool
    {
        $row = $this->connection->medoo()->get('users', '*', ['email' => $email, 'status' => 'active']);

        if (!$row || !password_verify($password, $row['password_hash'])) {
            return false;
        }

        $this->login($this->hydrate($row));

        return true;
    }

    public function login(User $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_capabilities'] = $user->capabilities;
        $this->resolvedUser = $user;

        $this->connection->update('users', ['last_login_at' => date('Y-m-d H:i:s')], ['id' => $user->id]);
    }

    public function logout(): void
    {
        $this->resolvedUser = null;
        session_unset();
        session_destroy();
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function user(): ?User
    {
        if ($this->attemptedResolve) {
            return $this->resolvedUser;
        }

        $this->attemptedResolve = true;

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return null;
        }

        $row = $this->connection->medoo()->get('users', '*', ['id' => $userId, 'status' => 'active']);

        if (!$row) {
            return null;
        }

        $user = $this->hydrate($row);
        $user->capabilities = $_SESSION['user_capabilities'] ?? $user->capabilities;

        return $this->resolvedUser = $user;
    }

    private function hydrate(array $row): User
    {
        $roleRow = $this->connection->medoo()->get('roles', '*', ['id' => $row['role_id']]);
        $row['role_slug'] = $roleRow['slug'] ?? '';

        $capabilityRows = $this->connection->medoo()->select('role_capabilities', ['[><]capabilities' => ['capability_id' => 'id']], ['capabilities.slug'], [
            'role_capabilities.role_id' => $row['role_id'],
        ]);

        return User::fromRow($row, array_column($capabilityRows, 'slug'));
    }
}
