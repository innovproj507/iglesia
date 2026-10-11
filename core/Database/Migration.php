<?php

namespace Cms\Database;

abstract class Migration
{
    public function __construct(protected Connection $connection)
    {
    }

    abstract public function up(): void;

    abstract public function down(): void;

    protected function exec(string $sql): void
    {
        $this->connection->pdo()->exec($sql);
    }
}
