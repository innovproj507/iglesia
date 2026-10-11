<?php

return new class(app(\Cms\Database\Connection::class)) extends \Cms\Database\Migration {
    public function up(): void
    {
        $this->exec("CREATE TABLE terms (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            taxonomy VARCHAR(50) NOT NULL,
            name VARCHAR(150) NOT NULL,
            slug VARCHAR(150) NOT NULL,
            UNIQUE KEY terms_taxonomy_slug_unique (taxonomy, slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->exec("CREATE TABLE term_relationships (
            post_id INT UNSIGNED NOT NULL,
            term_id INT UNSIGNED NOT NULL,
            PRIMARY KEY (post_id, term_id),
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
            FOREIGN KEY (term_id) REFERENCES terms(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down(): void
    {
        $this->exec('DROP TABLE IF EXISTS term_relationships');
        $this->exec('DROP TABLE IF EXISTS terms');
    }
};
