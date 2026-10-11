<?php

return new class(app(\Cms\Database\Connection::class)) extends \Cms\Database\Migration {
    public function up(): void
    {
        $this->exec("CREATE TABLE plugins (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(100) NOT NULL UNIQUE,
            name VARCHAR(150) NOT NULL,
            version_installed VARCHAR(20) NOT NULL,
            status ENUM('active','inactive') NOT NULL DEFAULT 'inactive',
            installed_at DATETIME NOT NULL,
            activated_at DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down(): void
    {
        $this->exec('DROP TABLE IF EXISTS plugins');
    }
};
