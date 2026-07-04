<?php
class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        if (!file_exists(__DIR__ . '/../config.php')) {
            header('Location: /admin.php?route=setup');
            exit;
        }

        require_once __DIR__ . '/../config.php';

        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            if (defined('DB_NAME')) {
                header('Location: /admin.php?route=setup');
                exit;
            }
            die('Error de conexión a la base de datos.');
        }

        return self::$instance;
    }
}
