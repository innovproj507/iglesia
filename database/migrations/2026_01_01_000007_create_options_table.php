<?php

return new class(app(\Cms\Database\Connection::class)) extends \Cms\Database\Migration {
    public function up(): void
    {
        $this->exec("CREATE TABLE options (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            option_name VARCHAR(191) NOT NULL UNIQUE,
            option_value LONGTEXT NULL,
            autoload TINYINT(1) NOT NULL DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down(): void
    {
        $this->exec('DROP TABLE IF EXISTS options');
    }
};
