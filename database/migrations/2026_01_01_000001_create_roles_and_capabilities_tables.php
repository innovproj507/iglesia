<?php

return new class(app(\Cms\Database\Connection::class)) extends \Cms\Database\Migration {
    public function up(): void
    {
        $this->exec("CREATE TABLE roles (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(60) NOT NULL UNIQUE,
            name VARCHAR(100) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->exec("CREATE TABLE capabilities (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(60) NOT NULL UNIQUE,
            name VARCHAR(100) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->exec("CREATE TABLE role_capabilities (
            role_id INT UNSIGNED NOT NULL,
            capability_id INT UNSIGNED NOT NULL,
            PRIMARY KEY (role_id, capability_id),
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
            FOREIGN KEY (capability_id) REFERENCES capabilities(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down(): void
    {
        $this->exec('DROP TABLE IF EXISTS role_capabilities');
        $this->exec('DROP TABLE IF EXISTS capabilities');
        $this->exec('DROP TABLE IF EXISTS roles');
    }
};
