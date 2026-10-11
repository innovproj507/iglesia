<?php

return new class(app(\Cms\Database\Connection::class)) extends \Cms\Database\Migration {
    public function up(): void
    {
        $this->exec("CREATE TABLE users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            uuid CHAR(36) NOT NULL UNIQUE,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(191) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role_id INT UNSIGNED NOT NULL,
            status ENUM('active','suspended') NOT NULL DEFAULT 'active',
            avatar_media_id INT UNSIGNED NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            last_login_at DATETIME NULL,
            FOREIGN KEY (role_id) REFERENCES roles(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down(): void
    {
        $this->exec('DROP TABLE IF EXISTS users');
    }
};
