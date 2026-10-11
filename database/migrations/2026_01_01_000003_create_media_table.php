<?php

return new class(app(\Cms\Database\Connection::class)) extends \Cms\Database\Migration {
    public function up(): void
    {
        $this->exec("CREATE TABLE media (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            uploader_id INT UNSIGNED NULL,
            file_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            size INT UNSIGNED NOT NULL DEFAULT 0,
            alt_text VARCHAR(255) NULL,
            meta JSON NULL,
            created_at DATETIME NOT NULL,
            FOREIGN KEY (uploader_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down(): void
    {
        $this->exec('DROP TABLE IF EXISTS media');
    }
};
