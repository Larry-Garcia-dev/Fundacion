<?php
// ============================================================
// PRODUCCIÓN: Llena estas credenciales para que el instalador
// se ejecute automáticamente al visitar la página de setup.
// Deja en vacío para usar el formulario manual.
// ============================================================
$PROD_DB_HOST = '';       // ej: 'localhost' o 'mysql.hostinger.com'
$PROD_DB_NAME = '';       // ej: 'u123456789_landing'
$PROD_DB_USER = '';       // ej: 'u123456789_admin'
$PROD_DB_PASS = '';       // contraseña de la BD
$PROD_ADMIN_USER = '';    // ej: 'admin'
$PROD_ADMIN_EMAIL = '';   // ej: 'admin@fundacionvisiondefuturo.org'
$PROD_ADMIN_PASS = '';    // contraseña del admin (mín 6 chars)
// ============================================================

class SetupController
{
    public function indexAction(): void
    {
        global $PROD_DB_HOST, $PROD_DB_NAME, $PROD_DB_USER, $PROD_DB_PASS,
               $PROD_ADMIN_USER, $PROD_ADMIN_EMAIL, $PROD_ADMIN_PASS;

        $hasProd = !empty($PROD_DB_HOST) && !empty($PROD_DB_NAME)
            && !empty($PROD_DB_USER) && !empty($PROD_ADMIN_USER)
            && !empty($PROD_ADMIN_EMAIL) && !empty($PROD_ADMIN_PASS);

        // Auto-ejecutar con credenciales de producción en GET
        if ($hasProd && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $_POST = [
                'db_host'      => $PROD_DB_HOST,
                'db_name'      => $PROD_DB_NAME,
                'db_user'      => $PROD_DB_USER,
                'db_pass'      => $PROD_DB_PASS,
                'admin_user'   => $PROD_ADMIN_USER,
                'admin_email'  => $PROD_ADMIN_EMAIL,
                'admin_pass'   => $PROD_ADMIN_PASS,
            ];
            $_SERVER['REQUEST_METHOD'] = 'POST';
        }

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
            "CREATE TABLE IF NOT EXISTS `org_values` (
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
            'hero_title' => 'Comprometidos con el bienestar, la calidad y el desarrollo social.',
            'hero_subtitle' => 'Prestamos servicios integrales para instituciones, especialmente del sector salud, generando bienestar mediante procesos responsables, seguros y desarrollados bajo altos estándares de calidad.',
            'hero_btn_primary' => 'Solicitar información',
            'hero_btn_secondary' => 'Conoce nuestros servicios',
            'about_title' => '¿Quiénes somos?',
            'about_subtitle' => 'Construimos bienestar a través de servicios integrales.',
            'about_content_1' => 'La Fundación Visión de Futuro – Servicios Integrales (FUNVIFUT) es una organización comprometida con mejorar la calidad de vida de las comunidades mediante la prestación de servicios integrales, destacándose por su experiencia en alimentación institucional para el sector salud.',
            'about_content_2' => 'Nuestro trabajo combina calidad, inocuidad, responsabilidad social y un equipo humano altamente comprometido para generar impacto positivo en las instituciones y en las personas que más lo necesitan.',
            'commitment_title' => 'Nuestro compromiso',
            'commitment_subtitle' => 'Más que prestar un servicio, construimos bienestar.',
            'commitment_content' => 'Cada proyecto representa una oportunidad para fortalecer instituciones, mejorar la calidad de vida de las personas y aportar al desarrollo sostenible del país.',
            'mission_title' => 'Nuestra Misión',
            'mission_content' => 'Contribuir al bienestar y la calidad de vida de la comunidad mediante la prestación de servicios integrales, especialmente para el sector salud, desarrollados con altos estándares de calidad, inocuidad, responsabilidad social y talento humano competente.',
            'vision_title' => 'Nuestra Visión',
            'vision_content' => 'Para 2030 ser reconocidos a nivel nacional como una organización líder en servicios integrales para el sector salud y en el desarrollo de proyectos sociales, destacándonos por nuestra excelencia, innovación, compromiso con la calidad y generación de bienestar.',
            'together_title' => '¿Trabajemos juntos?',
            'together_content' => 'Estamos preparados para desarrollar soluciones integrales que aporten valor a instituciones públicas y privadas.',
            'together_btn' => 'Solicitar una propuesta',
            'theme_color_primary' => '#9b98d5', 'theme_color_secondary' => '#86d2f1',
            'theme_color_dark' => '#1e293b', 'logo_path' => 'logo1.png',
            'footer_address' => 'Ibagué – Tolima',
            'footer_email' => 'contacto@fundacionvisiondefuturo.org',
            'footer_phone' => '+57 300 123 4567',
            'footer_facebook' => 'https://facebook.com',
            'footer_instagram' => 'https://instagram.com',
            'footer_twitter' => 'https://twitter.com',
            'footer_linkedin' => 'https://linkedin.com',
        ];
        $stmt = $pdo->prepare("INSERT INTO `settings` (key_name, value_text) VALUES (?, ?)");
        foreach ($defaults as $k => $v) { $stmt->execute([$k, $v]); }

        $services = [
            ['Alimentación Institucional', 'Planeación, preparación y suministro de alimentos bajo estrictos protocolos de calidad e inocuidad para instituciones del sector salud.', 'utensils', 1],
            ['Servicios Integrales', 'Desarrollamos soluciones operativas adaptadas a las necesidades de cada institución.', 'briefcase', 2],
            ['Proyectos Sociales', 'Diseñamos e implementamos iniciativas que generan bienestar y fortalecen comunidades.', 'users', 3],
            ['Acompañamiento Institucional', 'Trabajamos de la mano con entidades públicas y privadas aportando soluciones responsables y sostenibles.', 'handshake', 4],
        ];
        $stmtSrv = $pdo->prepare("INSERT INTO `services` (title, description, icon, display_order) VALUES (?, ?, ?, ?)");
        foreach ($services as $s) { $stmtSrv->execute($s); }

        $benefits = [
            ['Calidad', 'Procesos desarrollados bajo altos estándares.', 'award', 1],
            ['Inocuidad', 'Compromiso permanente con la seguridad alimentaria.', 'shield', 2],
            ['Responsabilidad Social', 'Trabajamos para generar un impacto positivo en las comunidades.', 'heart', 3],
            ['Talento Humano', 'Un equipo competente, comprometido y orientado al servicio.', 'user-check', 4],
        ];
        $stmtBen = $pdo->prepare("INSERT INTO `benefits` (title, description, icon, display_order) VALUES (?, ?, ?, ?)");
        foreach ($benefits as $b) { $stmtBen->execute($b); }

        $values = [
            ['Compromiso', 1], ['Calidad', 2], ['Responsabilidad', 3], ['Transparencia', 4],
            ['Innovación', 5], ['Trabajo en equipo', 6], ['Vocación de servicio', 7], ['Respeto', 8],
        ];
        $stmtVal = $pdo->prepare("INSERT INTO `org_values` (title, display_order) VALUES (?, ?)");
        foreach ($values as $v) { $stmtVal->execute($v); }
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
