<?php

namespace Cms\Auth;

class User
{
    public int $id;
    public string $uuid;
    public string $name;
    public string $email;
    public string $passwordHash;
    public int $roleId;
    public string $roleSlug;
    public string $status;

    /** @var string[] */
    public array $capabilities;

    public static function fromRow(array $row, array $capabilities): self
    {
        $user = new self();
        $user->id = (int) $row['id'];
        $user->uuid = $row['uuid'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->passwordHash = $row['password_hash'];
        $user->roleId = (int) $row['role_id'];
        $user->roleSlug = $row['role_slug'] ?? '';
        $user->status = $row['status'];
        $user->capabilities = $capabilities;

        return $user;
    }

    public function can(string $capability): bool
    {
        return in_array($capability, $this->capabilities, true);
    }

    public function isAdmin(): bool
    {
        return $this->roleSlug === 'admin';
    }
}
