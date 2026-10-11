<?php

return new class(app(\Cms\Database\Connection::class)) extends \Cms\Database\Migration {
    public function up(): void
    {
        $this->exec("CREATE TABLE posts (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            uuid CHAR(36) NOT NULL UNIQUE,
            author_id INT UNSIGNED NOT NULL,
            post_type VARCHAR(50) NOT NULL DEFAULT 'post',
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            content LONGTEXT NULL,
            excerpt TEXT NULL,
            status ENUM('draft','published','scheduled','trashed') NOT NULL DEFAULT 'draft',
            featured_media_id INT UNSIGNED NULL,
            published_at DATETIME NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE KEY posts_type_slug_unique (post_type, slug),
            INDEX posts_type_status_idx (post_type, status),
            FOREIGN KEY (author_id) REFERENCES users(id),
            FOREIGN KEY (featured_media_id) REFERENCES media(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->exec("CREATE TABLE postmeta (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            post_id INT UNSIGNED NOT NULL,
            meta_key VARCHAR(191) NOT NULL,
            meta_value LONGTEXT NULL,
            INDEX postmeta_post_key_idx (post_id, meta_key),
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down(): void
    {
        $this->exec('DROP TABLE IF EXISTS postmeta');
        $this->exec('DROP TABLE IF EXISTS posts');
    }
};
