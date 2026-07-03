<?php
// Evitar loops en el setup
$current_script = basename($_SERVER['SCRIPT_NAME']);

if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
} else {
    if ($current_script !== 'setup.php') {
        header('Location: admin/setup.php');
        exit;
    }
}

$pdo = null;

try {
    // Intentamos conectar al servidor de base de datos
    $dsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
    $pdo_temp = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Verificar si existe la base de datos
    $query = $pdo_temp->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
    $db_exists = $query->fetch();
    
    if (!$db_exists) {
        if ($current_script !== 'setup.php') {
            $path_prefix = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '' : 'admin/';
            header('Location: ' . $path_prefix . 'setup.php');
            exit;
        }
    } else {
        // Si existe, nos conectamos a ella formalmente
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        
        // --- MIGRACIONES AUTOMÁTICAS DE LA BASE DE DATOS ---
        // 1. Crear tabla de galería
        $pdo->exec("CREATE TABLE IF NOT EXISTS `gallery` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `image_url` VARCHAR(255) NOT NULL,
            `caption` VARCHAR(255) NULL,
            `display_order` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;");

        // 2. Crear tabla de obras de caridad (proyectos)
        $pdo->exec("CREATE TABLE IF NOT EXISTS `charity_works` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(100) NOT NULL,
            `description` TEXT NOT NULL,
            `image_url` VARCHAR(255) NOT NULL,
            `display_order` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;");

        // 3. Poblar datos por defecto de galería si está vacía
        $check_gal = $pdo->query("SELECT COUNT(*) FROM `gallery`")->fetchColumn();
        if ($check_gal == 0) {
            $default_gallery = [
                ['https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=800&q=80', 'Entrega de alimentos a familias de nuestra comunidad.', 1],
                ['https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=800&q=80', 'Niños felices recibiendo su comida diaria.', 2],
                ['https://images.unsplash.com/photo-1594708767771-a7502209ff7e?auto=format&fit=crop&w=800&q=80', 'Voluntarios preparando raciones nutritivas.', 3],
                ['https://images.unsplash.com/photo-1502086223501-7ea6ecd79368?auto=format&fit=crop&w=800&q=80', 'Comprometidos con el desarrollo integral.', 4]
            ];
            $stmt_gal = $pdo->prepare("INSERT INTO `gallery` (image_url, caption, display_order) VALUES (?, ?, ?)");
            foreach ($default_gallery as $gal) {
                $stmt_gal->execute($gal);
            }
        }

        // 4. Poblar datos de obras de caridad si está vacía
        $check_works = $pdo->query("SELECT COUNT(*) FROM `charity_works`")->fetchColumn();
        if ($check_works == 0) {
            $default_works = [
                ['Suministro Nutricional Infantil', 'Entrega diaria de comidas y complementos alimenticios balanceados a más de 120 niños en comedores comunitarios del departamento.', 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=800&q=80', 1],
                ['Talleres de Apoyo y Alimentación', 'Combinamos el suministro alimentario con programas de reforzamiento escolar y talleres de valores para el crecimiento sano.', 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=800&q=80', 2],
                ['Jornadas de Alimentación Comunitaria', 'Distribución de alimentos y raciones nutritivas a familias en zonas vulnerables, garantizando seguridad alimentaria y bienestar infantil.', 'https://images.unsplash.com/photo-1594708767771-a7502209ff7e?auto=format&fit=crop&w=800&q=80', 3]
            ];
            $stmt_wrk = $pdo->prepare("INSERT INTO `charity_works` (title, description, image_url, display_order) VALUES (?, ?, ?, ?)");
            foreach ($default_works as $wrk) {
                $stmt_wrk->execute($wrk);
            }
        }

        // 5. Crear tabla de testimonios
        $pdo->exec("CREATE TABLE IF NOT EXISTS `testimonials` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(150) NOT NULL,
            `phone` VARCHAR(30) NULL,
            `gender` ENUM('male','female','other') NOT NULL DEFAULT 'other',
            `message` TEXT NOT NULL,
            `photo_url` VARCHAR(255) NULL,
            `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;");

        // 6. Poblar testimonio de ejemplo si está vacía
        $check_test = $pdo->query("SELECT COUNT(*) FROM `testimonials`")->fetchColumn();
        if ($check_test == 0) {
            $pdo->exec("INSERT INTO `testimonials` (name, email, phone, gender, message, photo_url, status) VALUES (
                'María García López',
                'maria.garcia@ejemplo.com',
                '3001234567',
                'female',
                'Gracias a la Fundación Visión de Futuro, mi hijo recibió alimentación adecuada y apoyo educativo durante todo el año. El trato del equipo fue excepcional y realmente se nota el compromiso con la comunidad. ¡Son un ejemplo de labor social!',
                NULL,
                'approved'
            )");
        }

        // 7. Migrar colores del logo e inputs de logo en settings (SOLO si no existen en la BD)
        // Lila: #9b98d5, Celeste: #86d2f1, Oscuro: #1e293b
        $new_settings = [
            'theme_color_primary' => '#9b98d5',
            'theme_color_secondary' => '#86d2f1',
            'theme_color_dark' => '#1e293b',
            'logo_path' => 'logo1.png',
            'logo_size' => '72',
            'logo_offset_x' => '0',
            'logo_offset_y' => '0'
        ];
        
        foreach ($new_settings as $key => $val) {
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM `settings` WHERE `key_name` = ?");
            $check_stmt->execute([$key]);
            if ($check_stmt->fetchColumn() == 0) {
                $ins_stmt = $pdo->prepare("INSERT INTO `settings` (key_name, value_text) VALUES (?, ?)");
                $ins_stmt->execute([$key, $val]);
            }
        }

        // Nota: logo_path y logo_size ya no se fuerzan aquí para permitir
        // la personalización desde el panel de administración.

        // Crear settings de logos independientes si no existen
        $extra_logos = [
            'hero_logo_path' => 'logo1.png',
            'contact_logo_path' => 'logo1.png',
            'footer_logo_path' => 'logo1.png'
        ];
        foreach ($extra_logos as $key => $val) {
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM `settings` WHERE `key_name` = ?");
            $check_stmt->execute([$key]);
            if ($check_stmt->fetchColumn() == 0) {
                $ins_stmt = $pdo->prepare("INSERT INTO `settings` (key_name, value_text) VALUES (?, ?)");
                $ins_stmt->execute([$key, $val]);
            }
        }

        // Setting para activar/desactivar sección de testimonios
        $check_test_setting = $pdo->prepare("SELECT COUNT(*) FROM `settings` WHERE `key_name` = 'testimonials_enabled'");
        $check_test_setting->execute();
        if ($check_test_setting->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO `settings` (key_name, value_text) VALUES ('testimonials_enabled', '1')");
        }
    }
} catch (PDOException $e) {
    if ($current_script !== 'setup.php') {
        // En lugar de un simple error, redirigir o mostrar vista de instalación amigable
        $path_prefix = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '' : 'admin/';
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Configuración Requerida - Fundación Visión de Futuro</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; color: #0f172a; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; padding: 20px; text-align: center; }
                .card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); max-width: 500px; width: 100%; border-top: 4px solid #2563eb; }
                h1 { color: #1e3a8a; margin-top: 0; font-size: 24px; }
                p { color: #475569; line-height: 1.6; font-size: 16px; margin-bottom: 24px; }
                .btn { display: inline-block; background: #2563eb; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; transition: background 0.2s; }
                .btn:hover { background: #1d4ed8; }
            </style>
        </head>
        <body>
            <div class='card'>
                <h1>¡Configuración Inicial Requerida!</h1>
                <p>No se pudo establecer una conexión con la base de datos MySQL. Esto es normal si es la primera vez que ejecutas el proyecto.</p>
                <p>Haz clic en el botón inferior para ingresar las credenciales de tu base de datos e instalar el esquema con el usuario administrador inicial.</p>
                <a href='{$path_prefix}setup.php' class='btn'>Iniciar Instalación y Configuración</a>
            </div>
        </body>
        </html>";
        exit;
    }
}
?>
