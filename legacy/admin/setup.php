<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$step = isset($_POST['action']) ? 'install' : 'form';
$error = '';
$success = '';

if ($step === 'install') {
    $db_host = trim($_POST['db_host'] ?? 'localhost');
    $db_name = trim($_POST['db_name'] ?? 'landing_db');
    $db_user = trim($_POST['db_user'] ?? 'root');
    $db_pass = $_POST['db_pass'] ?? '';
    
    $admin_user = trim($_POST['admin_user'] ?? 'admin');
    $admin_email = trim($_POST['admin_email'] ?? 'admin@visiondefuturo.org');
    $admin_pass = $_POST['admin_pass'] ?? '';
    
    if (empty($db_host) || empty($db_name) || empty($db_user) || empty($admin_user) || empty($admin_email) || empty($admin_pass)) {
        $error = 'Todos los campos excepto la contraseña de la base de datos son obligatorios.';
        $step = 'form';
    } elseif (strlen($admin_pass) < 6) {
        $error = 'La contraseña del administrador debe tener al menos 6 caracteres.';
        $step = 'form';
    } else {
        try {
            // 1. Intentar conectar a MySQL (sin especificar DB por si no existe aún)
            $dsn = "mysql:host=$db_host;charset=utf8mb4";
            $conn = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5
            ]);
            
            // 2. Crear la base de datos si no existe
            $conn->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // 3. Conectar a la base de datos recién creada
            $conn->exec("USE `$db_name`");
            
            // 4. Crear tabla de usuarios
            $conn->exec("CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(50) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `email` VARCHAR(100) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;");
            
            // 5. Crear tabla de configuraciones
            $conn->exec("CREATE TABLE IF NOT EXISTS `settings` (
                `key_name` VARCHAR(50) PRIMARY KEY,
                `value_text` TEXT NULL,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;");
            
            // 6. Crear tabla de servicios
            $conn->exec("CREATE TABLE IF NOT EXISTS `services` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(100) NOT NULL,
                `description` TEXT NOT NULL,
                `icon` VARCHAR(50) NOT NULL,
                `display_order` INT DEFAULT 0
            ) ENGINE=InnoDB;");
            
            // 7. Crear tabla de beneficios
            $conn->exec("CREATE TABLE IF NOT EXISTS `benefits` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(100) NOT NULL,
                `description` TEXT NOT NULL,
                `icon` VARCHAR(50) NOT NULL,
                `display_order` INT DEFAULT 0
            ) ENGINE=InnoDB;");
            
            // 8. Crear tabla de valores
            $conn->exec("CREATE TABLE IF NOT EXISTS `values` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(100) NOT NULL,
                `display_order` INT DEFAULT 0
            ) ENGINE=InnoDB;");
            
            // 9. Crear tabla de mensajes
            $conn->exec("CREATE TABLE IF NOT EXISTS `contact_messages` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(100) NOT NULL,
                `phone` VARCHAR(20) NULL,
                `message` TEXT NOT NULL,
                `status` ENUM('unread', 'read', 'archived') DEFAULT 'unread',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;");
            
            // 10. Limpiar datos viejos por si acaso
            $conn->exec("DELETE FROM `users` WHERE `username` = " . $conn->quote($admin_user));
            $conn->exec("DELETE FROM `settings` WHERE 1");
            $conn->exec("DELETE FROM `services` WHERE 1");
            $conn->exec("DELETE FROM `benefits` WHERE 1");
            $conn->exec("DELETE FROM `values` WHERE 1");
            
            // 11. Insertar el Administrador Inicial
            $hashed_pass = password_hash($admin_pass, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO `users` (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute([$admin_user, $hashed_pass, $admin_email]);
            
            // 12. Insertar Configuraciones Predeterminadas (de acuerdo al PDF schema)
            $default_settings = [
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
                
                'footer_address' => 'Ibagué – Tolima',
                'footer_email' => 'contacto@fundacionvisiondefuturo.org',
                'footer_phone' => '+57 300 123 4567',
                'footer_facebook' => 'https://facebook.com',
                'footer_instagram' => 'https://instagram.com',
                'footer_twitter' => 'https://twitter.com',
                'footer_linkedin' => 'https://linkedin.com'
            ];
            
            $stmt_set = $conn->prepare("INSERT INTO `settings` (key_name, value_text) VALUES (?, ?)");
            foreach ($default_settings as $key => $val) {
                $stmt_set->execute([$key, $val]);
            }
            
            // 13. Insertar Servicios Predeterminados
            $default_services = [
                ['Alimentación Institucional', 'Planeación, preparación y suministro de alimentos bajo estrictos protocolos de calidad e inocuidad para instituciones del sector salud.', 'utensils', 1],
                ['Servicios Integrales', 'Desarrollamos soluciones operativas adaptadas a las necesidades de cada institución.', 'briefcase', 2],
                ['Proyectos Sociales', 'Diseñamos e implementamos iniciativas que generan bienestar y fortalecen comunidades.', 'users', 3],
                ['Acompañamiento Institucional', 'Trabajamos de la mano con entidades públicas y privadas aportando soluciones responsables y sostenibles.', 'handshake', 4]
            ];
            
            $stmt_srv = $conn->prepare("INSERT INTO `services` (title, description, icon, display_order) VALUES (?, ?, ?, ?)");
            foreach ($default_services as $srv) {
                $stmt_srv->execute($srv);
            }
            
            // 14. Insertar Beneficios Predeterminados (¿Por qué elegirnos?)
            $default_benefits = [
                ['Calidad', 'Procesos desarrollados bajo altos estándares.', 'award', 1],
                ['Inocuidad', 'Compromiso permanente con la seguridad alimentaria.', 'shield', 2],
                ['Responsabilidad Social', 'Trabajamos para generar un impacto positivo en las comunidades.', 'heart', 3],
                ['Talento Humano', 'Un equipo competente, comprometido y orientado al servicio.', 'user-check', 4]
            ];
            
            $stmt_ben = $conn->prepare("INSERT INTO `benefits` (title, description, icon, display_order) VALUES (?, ?, ?, ?)");
            foreach ($default_benefits as $ben) {
                $stmt_ben->execute($ben);
            }
            
            // 15. Insertar Valores Predeterminados
            $default_values = [
                ['Compromiso', 1],
                ['Calidad', 2],
                ['Responsabilidad', 3],
                ['Transparencia', 4],
                ['Innovación', 5],
                ['Trabajo en equipo', 6],
                ['Vocación de servicio', 7],
                ['Respeto', 8]
            ];
            
            $stmt_val = $conn->prepare("INSERT INTO `values` (title, display_order) VALUES (?, ?)");
            foreach ($default_values as $val) {
                $stmt_val->execute($val);
            }
            
            // 16. Escribir el archivo config.php
            $config_content = "<?php\n"
                            . "// Archivo de configuración generado automáticamente por setup.php\n"
                            . "define('DB_HOST', '" . addslashes($db_host) . "');\n"
                            . "define('DB_NAME', '" . addslashes($db_name) . "');\n"
                            . "define('DB_USER', '" . addslashes($db_user) . "');\n"
                            . "define('DB_PASS', '" . addslashes($db_pass) . "');\n\n"
                            . "define('SITE_NAME', 'Fundación Visión de Futuro');\n"
                            . "?>";
                            
            file_put_contents(__DIR__ . '/../config.php', $config_content);
            
            $success = '¡Instalación completada exitosamente! Se ha creado la base de datos, configurado las tablas con los contenidos iniciales y creado tu cuenta de administrador.';
            $step = 'success';
            
        } catch (PDOException $e) {
            $error = 'Error de Base de Datos: ' . $e->getMessage();
            $step = 'form';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador del Proyecto - Fundación Visión de Futuro</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f8fafc;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary: #0d9488;
            --dark: #0f172a;
            --light: #ffffff;
            --text-main: #334155;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --danger: #ef4444;
            --success: #10b981;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .setup-container {
            background-color: var(--light);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 650px;
            overflow: hidden;
            border-top: 6px solid var(--primary);
        }
        
        .setup-header {
            padding: 30px 40px;
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            color: var(--light);
            text-align: center;
        }
        
        .setup-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .setup-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }
        
        .setup-body {
            padding: 40px;
        }
        
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        
        .form-section:last-of-type {
            border-bottom: none;
            margin-bottom: 10px;
        }
        
        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title span {
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 6px;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }
        
        .form-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        
        .alert {
            padding: 14px 20px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
        }
        
        .alert-success {
            background-color: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }
        
        .btn-submit {
            display: block;
            width: 100%;
            background-color: var(--primary);
            color: var(--light);
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            text-align: center;
            text-decoration: none;
        }
        
        .btn-submit:hover {
            background-color: var(--primary-hover);
        }
        
        .success-box {
            text-align: center;
            padding: 20px 0;
        }
        
        .success-icon {
            font-size: 50px;
            color: var(--success);
            margin-bottom: 20px;
        }
        
        .success-box h2 {
            font-family: 'Outfit', sans-serif;
            color: var(--dark);
            margin-bottom: 12px;
            font-size: 22px;
        }
        
        .success-box p {
            color: var(--text-muted);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

    <div class="setup-container">
        <div class="setup-header">
            <h1>Fundación Visión de Futuro</h1>
            <p>Asistente de Configuración e Instalación del Sistema</p>
        </div>
        
        <div class="setup-body">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($step === 'form'): ?>
                <form action="setup.php" method="POST">
                    <input type="hidden" name="action" value="install">
                    
                    <!-- Sección de Base de Datos -->
                    <div class="form-section">
                        <h2 class="section-title"><span>1</span> Configuración de la Base de Datos</h2>
                        
                        <div class="form-group">
                            <label for="db_host">Servidor MySQL (Host)</label>
                            <input type="text" id="db_host" name="db_host" value="<?php echo isset($_POST['db_host']) ? htmlspecialchars($_POST['db_host']) : 'localhost'; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="db_name">Nombre de la Base de Datos</label>
                            <input type="text" id="db_name" name="db_name" value="<?php echo isset($_POST['db_name']) ? htmlspecialchars($_POST['db_name']) : 'landing_db'; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="db_user">Usuario de MySQL</label>
                            <input type="text" id="db_user" name="db_user" value="<?php echo isset($_POST['db_user']) ? htmlspecialchars($_POST['db_user']) : 'root'; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="db_pass">Contraseña de MySQL</label>
                            <input type="password" id="db_pass" name="db_pass" value="<?php echo isset($_POST['db_pass']) ? htmlspecialchars($_POST['db_pass']) : ''; ?>" placeholder="Dejar en blanco si no tiene">
                        </div>
                    </div>
                    
                    <!-- Sección de Administrador -->
                    <div class="form-section">
                        <h2 class="section-title"><span>2</span> Cuenta del Administrador de la Web</h2>
                        
                        <div class="form-group">
                            <label for="admin_user">Nombre de Usuario del Admin</label>
                            <input type="text" id="admin_user" name="admin_user" value="<?php echo isset($_POST['admin_user']) ? htmlspecialchars($_POST['admin_user']) : 'admin'; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_email">Correo Electrónico del Admin</label>
                            <input type="email" id="admin_email" name="admin_email" value="<?php echo isset($_POST['admin_email']) ? htmlspecialchars($_POST['admin_email']) : 'admin@visiondefuturo.org'; ?>" placeholder="admin@visiondefuturo.org" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_pass">Contraseña del Admin (Mínimo 6 caracteres)</label>
                            <input type="password" id="admin_pass" name="admin_pass" placeholder="Elige una contraseña segura" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">Instalar y Configurar Sistema</button>
                </form>
                
            <?php elseif ($step === 'success'): ?>
                <div class="success-box">
                    <div class="success-icon">✓</div>
                    <h2>¡Configuración Completa!</h2>
                    <p><?php echo htmlspecialchars($success); ?></p>
                    
                    <div style="display: flex; gap: 15px;">
                        <a href="../index.php" class="btn-submit" style="background-color: var(--secondary);">Ver Landing Page</a>
                        <a href="login.php" class="btn-submit">Ir al Login de Administración</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
