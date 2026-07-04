<?php
class SetupController
{
    public function indexAction(): void
    {
        $step  = 'form';
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->runInstallation();
            $step  = $result['step'];
            $error = $result['error'];
        }
        View::render('admin/setup', ['step' => $step, 'error' => $error]);
    }

    private function runInstallation(): array
    {
        $db_host = trim($_POST['db_host'] ?? 'localhost');
        $db_name = trim($_POST['db_name'] ?? 'landing_db');
        $db_user = trim($_POST['db_user'] ?? 'root');
        $db_pass = $_POST['db_pass'] ?? '';
        $admin_user  = trim($_POST['admin_user'] ?? 'admin');
        $admin_email = trim($_POST['admin_email'] ?? 'admin@visiondefuturo.org');
        $admin_pass  = $_POST['admin_pass'] ?? '';

        if (empty($db_host) || empty($db_name) || empty($db_user)
            || empty($admin_user) || empty($admin_email) || empty($admin_pass)) {
            return ['step' => 'form', 'error' => 'Todos los campos excepto la contraseña de BD son obligatorios.'];
        }
        if (strlen($admin_pass) < 6) {
            return ['step' => 'form', 'error' => 'La contraseña del admin debe tener al menos 6 caracteres.'];
        }
        try {
            $pdo = $this->connectAndCreateDb($db_host, $db_name, $db_user, $db_pass);
            $this->createTables($pdo);
            $this->seedDefaults($pdo, $admin_user, $admin_email, $admin_pass);
            $this->writeConfigFile($db_host, $db_name, $db_user, $db_pass);
            return ['step' => 'success', 'error' => ''];
        } catch (PDOException $e) {
            return ['step' => 'form', 'error' => 'Error de Base de Datos: ' . $e->getMessage()];
        }
    }

    private function connectAndCreateDb(string $host, string $name, string $user, string $pass): PDO
    {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5,
        ]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$name`");
        return $pdo;
    }

    private function createTables(PDO $pdo): void
    {
        $tables = [
            "CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY, `username` VARCHAR(50) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL, `email` VARCHAR(100) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB",
            "CREATE TABLE IF NOT EXISTS `settings` (
                `key_name` VARCHAR(50) PRIMARY KEY, `value_text` TEXT NULL,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB",
            "CREATE TABLE IF NOT EXISTS `services` (
                `id` INT AUTO_INCREMENT PRIMARY KEY, `title` VARCHAR(100) NOT NULL,
                `description` TEXT NOT NULL, `icon` VARCHAR(50) NOT NULL,
                `display_order` INT DEFAULT 0) ENGINE=InnoDB",
            "CREATE TABLE IF NOT EXISTS `benefits` (
                `id` INT AUTO_INCREMENT PRIMARY KEY, `title` VARCHAR(100) NOT NULL,
                `description` TEXT NOT NULL, `icon` VARCHAR(50) NOT NULL,
                `display_order` INT DEFAULT 0) ENGINE=InnoDB",
            "CREATE TABLE IF NOT EXISTS `values` (
                `id` INT AUTO_INCREMENT PRIMARY KEY, `title` VARCHAR(100) NOT NULL,
                `display_order` INT DEFAULT 0) ENGINE=InnoDB",
            "CREATE TABLE IF NOT EXISTS `contact_messages` (
                `id` INT AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(100) NOT NULL, `phone` VARCHAR(20) NULL,
                `message` TEXT NOT NULL, `status` ENUM('unread','read','archived') DEFAULT 'unread',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB",
            "CREATE TABLE IF NOT EXISTS `gallery` (
                `id` INT AUTO_INCREMENT PRIMARY KEY, `image_url` VARCHAR(255) NOT NULL,
                `caption` VARCHAR(255) NULL, `display_order` INT DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB",
            "CREATE TABLE IF NOT EXISTS `charity_works` (
                `id` INT AUTO_INCREMENT PRIMARY KEY, `title` VARCHAR(100) NOT NULL,
                `description` TEXT NOT NULL, `image_url` VARCHAR(255) NOT NULL,
                `display_order` INT DEFAULT 0, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB",
            "CREATE TABLE IF NOT EXISTS `testimonials` (
                `id` INT AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(150) NOT NULL, `phone` VARCHAR(30) NULL,
                `gender` ENUM('male','female','other') NOT NULL DEFAULT 'other',
                `message` TEXT NOT NULL, `photo_url` VARCHAR(255) NULL,
                `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB",
        ];
        foreach ($tables as $sql) { $pdo->exec($sql); }
    }

    private function seedDefaults(PDO $pdo, string $user, string $email, string $pass): void
    {
        $pdo->exec("DELETE FROM `users` WHERE `username` = " . $pdo->quote($user));
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $pdo->prepare("INSERT INTO `users` (username, password, email) VALUES (?, ?, ?)")
            ->execute([$user, $hash, $email]);
        $defaults = [
            'hero_title' => 'Comprometidos con el bienestar y el desarrollo social.',
            'hero_subtitle' => 'Prestamos servicios integrales para instituciones del sector salud.',
            'hero_btn_primary' => 'Solicitar información',
            'hero_btn_secondary' => 'Conoce nuestros servicios',
            'theme_color_primary' => '#9b98d5', 'theme_color_secondary' => '#86d2f1',
            'theme_color_dark' => '#1e293b', 'logo_path' => 'logo1.png',
            'footer_address' => 'Ibagué – Tolima',
            'footer_email' => 'contacto@fundacionvisiondefuturo.org',
            'footer_phone' => '+57 300 123 4567',
        ];
        $stmt = $pdo->prepare("INSERT INTO `settings` (key_name, value_text) VALUES (?, ?)");
        foreach ($defaults as $k => $v) { $stmt->execute([$k, $v]); }
    }

    private function writeConfigFile(string $host, string $name, string $user, string $pass): void
    {
        $content = "<?php\n"
            . "define('DB_HOST', '" . addslashes($host) . "');\n"
            . "define('DB_NAME', '" . addslashes($name) . "');\n"
            . "define('DB_USER', '" . addslashes($user) . "');\n"
            . "define('DB_PASS', '" . addslashes($pass) . "');\n"
            . "define('SITE_NAME', 'Fundación Visión de Futuro');\n?>";
        file_put_contents(__DIR__ . '/../../config.php', $content);
    }
}
