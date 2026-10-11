<?php

namespace Cms\Database;

use Medoo\Medoo;
use PDO;

class Connection
{
    private Medoo $db;

    public function __construct(array $config)
    {
        $this->db = new Medoo([
            'type' => 'mysql',
            'host' => $config['host'],
            'port' => $config['port'] ?? 3306,
            'database' => $config['database'],
            'username' => $config['username'],
            'password' => $config['password'],
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'error' => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    public function medoo(): Medoo
    {
        return $this->db;
    }

    public function pdo(): PDO
    {
        return $this->db->pdo;
    }

    public function select(string $table, mixed $columns, array $where = []): array
    {
        return $this->db->select($table, $columns, $where);
    }

    public function get(string $table, mixed $columns, array $where = []): mixed
    {
        return $this->db->get($table, $columns, $where);
    }

    public function insert(string $table, array $data): string
    {
        $this->db->insert($table, $data);

        return $this->db->id();
    }

    public function update(string $table, array $data, array $where): void
    {
        $this->db->update($table, $data, $where);
    }

    public function delete(string $table, array $where): void
    {
        $this->db->delete($table, $where);
    }

    public function count(string $table, array $where = []): int
    {
        return (int) $this->db->count($table, $where);
    }
}
