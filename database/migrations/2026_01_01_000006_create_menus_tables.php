<?php

return new class(app(\Cms\Database\Connection::class)) extends \Cms\Database\Migration {
    public function up(): void
    {
        $this->exec("CREATE TABLE menus (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            location VARCHAR(50) NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->exec("CREATE TABLE menu_items (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            menu_id INT UNSIGNED NOT NULL,
            parent_id INT UNSIGNED NULL,
            label VARCHAR(150) NOT NULL,
            url VARCHAR(500) NULL,
            post_id INT UNSIGNED NULL,
            target VARCHAR(20) NOT NULL DEFAULT '_self',
            sort_order INT NOT NULL DEFAULT 0,
            FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE,
            FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE CASCADE,
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down(): void
    {
        $this->exec('DROP TABLE IF EXISTS menu_items');
        $this->exec('DROP TABLE IF EXISTS menus');
    }
};
