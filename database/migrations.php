<?php

class Migration
{
    public static function runAll(PDO $pdo): void
    {
        // Rename legacy 'values' table to 'org_values' if needed
        $tables = $pdo->query("SHOW TABLES LIKE 'values'")->fetchAll();
        if (!empty($tables)) {
            $pdo->exec("RENAME TABLE `values` TO `org_values`");
        }

        // Create all tables
        $pdo->exec("CREATE TABLE IF NOT EXISTS `services` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `icon` VARCHAR(255) DEFAULT '',
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `display_order` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `benefits` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `icon` VARCHAR(255) DEFAULT '',
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `display_order` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `org_values` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `display_order` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `gallery` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `image_url` VARCHAR(500) NOT NULL,
            `caption` VARCHAR(255) DEFAULT '',
            `display_order` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `charity_works` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `image_url` VARCHAR(500) DEFAULT '',
            `display_order` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `testimonials` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) DEFAULT '',
            `phone` VARCHAR(50) DEFAULT '',
            `gender` VARCHAR(20) DEFAULT '',
            `message` TEXT NOT NULL,
            `photo_url` VARCHAR(500) DEFAULT '',
            `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `contact_messages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) DEFAULT '',
            `phone` VARCHAR(50) DEFAULT '',
            `message` TEXT NOT NULL,
            `status` ENUM('unread','read','archived') DEFAULT 'unread',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(100) NOT NULL UNIQUE,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `key_name` VARCHAR(100) NOT NULL UNIQUE,
            `value_text` TEXT,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Seed default data if tables are empty
        self::seedIfEmpty($pdo);

        // Ensure all default settings keys exist
        self::ensureDefaultSettings($pdo);
    }

    private static function seedIfEmpty(PDO $pdo): void
    {
        // Seed default admin user
        $count = (int) $pdo->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
        if ($count === 0) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "INSERT INTO `users` (`username`, `email`, `password`) VALUES (:u, :e, :p)"
            );
            $stmt->execute([':u' => 'admin', ':e' => 'admin@fundacionvisiondefuturo.org', ':p' => $hash]);
        }

        // Seed default settings
        $count = (int) $pdo->query("SELECT COUNT(*) FROM `settings`")->fetchColumn();
        if ($count === 0) {
            $defaults = self::defaultSettings();
            $stmt = $pdo->prepare("INSERT INTO `settings` (`key_name`, `value_text`) VALUES (:k, :v)");
            foreach ($defaults as $key => $value) {
                $stmt->execute([':k' => $key, ':v' => $value]);
            }
        }
    }

    private static function ensureDefaultSettings(PDO $pdo): void
    {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `settings` WHERE `key_name` = :k");
        $insert = $pdo->prepare("INSERT INTO `settings` (`key_name`, `value_text`) VALUES (:k, :v)");

        foreach (self::defaultSettings() as $key => $value) {
            $stmt->execute([':k' => $key]);
            if ((int) $stmt->fetchColumn() === 0) {
                $insert->execute([':k' => $key, ':v' => $value]);
            }
        }
    }

    private static function defaultSettings(): array
    {
        return require __DIR__ . '/defaults.php';
    }
}
