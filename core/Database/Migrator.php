<?php

namespace Cms\Database;

class Migrator
{
    public function __construct(private Connection $connection)
    {
    }

    public function ensureMigrationsTable(): void
    {
        $this->connection->pdo()->exec(
            "CREATE TABLE IF NOT EXISTS migrations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(191) NOT NULL,
                `group` VARCHAR(100) NOT NULL DEFAULT 'core',
                batch INT UNSIGNED NOT NULL,
                executed_at DATETIME NOT NULL,
                UNIQUE KEY migrations_group_migration_unique (`group`, migration)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    /**
     * @return string[] list of migration files that were run
     */
    public function run(string $path, string $group = 'core'): array
    {
        $this->ensureMigrationsTable();

        $files = glob(rtrim($path, '/\\') . '/*.php') ?: [];
        sort($files);

        $already = $this->executed($group);
        $batch = $this->nextBatch($group);
        $ran = [];

        foreach ($files as $file) {
            $name = basename($file);

            if (in_array($name, $already, true)) {
                continue;
            }

            /** @var Migration $migration */
            $migration = require $file;
            $migration = is_object($migration) ? $migration : new $migration($this->connection);

            // MySQL DDL statements cause an implicit commit, so migrations are
            // not wrapped in a transaction; failures must be fixed and re-run.
            try {
                $migration->up();
            } catch (\Throwable $e) {
                throw new \RuntimeException("Migration failed [{$name}]: " . $e->getMessage(), previous: $e);
            }

            $stmt = $this->connection->pdo()->prepare(
                'INSERT INTO migrations (migration, `group`, batch, executed_at) VALUES (?, ?, ?, NOW())'
            );
            $stmt->execute([$name, $group, $batch]);
            $ran[] = $name;
        }

        return $ran;
    }

    private function executed(string $group): array
    {
        $stmt = $this->connection->pdo()->prepare('SELECT migration FROM migrations WHERE `group` = ?');
        $stmt->execute([$group]);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
    }

    private function nextBatch(string $group): int
    {
        $stmt = $this->connection->pdo()->prepare('SELECT MAX(batch) FROM migrations WHERE `group` = ?');
        $stmt->execute([$group]);
        $max = (int) $stmt->fetchColumn();

        return $max + 1;
    }
}
