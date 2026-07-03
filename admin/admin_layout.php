<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: login.php');
    exit;
}

// Incluir la conexión a la base de datos
require_once __DIR__ . '/../db.php';

function render_admin_header($title = 'Panel de Administración') {
    $current_page = basename($_SERVER['SCRIPT_NAME']);
    
    // Obtener cantidad de mensajes no leídos para la insignia
    global $pdo;
    $unread_count = 0;
    $pending_testimonials = 0;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM `contact_messages` WHERE `status` = 'unread'");
        $unread_count = $stmt->fetch()['total'];
    } catch (Exception $e) {
        // Ignorar si no existe la tabla todavía
    }
    try {
        $stmt_t = $pdo->query("SELECT COUNT(*) as total FROM `testimonials` WHERE `status` = 'pending'");
        $pending_testimonials = $stmt_t->fetch()['total'];
    } catch (Exception $e) {}

    // Obtener configuraciones de color dinámicas
    $color_primary = '#2563eb';
    $color_secondary = '#0d9488';
    $color_dark = '#0f172a';
    try {
        $stmt_colors = $pdo->query("SELECT * FROM `settings` WHERE `key_name` IN ('theme_color_primary', 'theme_color_secondary', 'theme_color_dark')");
        while ($row = $stmt_colors->fetch()) {
            if ($row['key_name'] === 'theme_color_primary') $color_primary = $row['value_text'];
            if ($row['key_name'] === 'theme_color_secondary') $color_secondary = $row['value_text'];
            if ($row['key_name'] === 'theme_color_dark') $color_dark = $row['value_text'];
        }
    } catch (Exception $e) {}
    
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($title) . ' - Fundación Visión de Futuro</title>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../assets/css/admin.css">
        <!-- Colores Dinámicos alineados con el Logo -->
        <style>
            :root {
                --primary: ' . $color_primary . ';
                --primary-hover: ' . $color_primary . 'cc;
                --secondary: ' . $color_secondary . ';
                --secondary-hover: ' . $color_secondary . 'cc;
                --sidebar-bg: ' . $color_dark . ';
            }
        </style>
        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>
    </head>
    <body>
        <!-- Animated Background Blobs -->
        <div class="admin-bg-blobs">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="blob blob-3"></div>
        </div>
        <!-- Custom Trailing Cursor -->
        <div class="custom-cursor" id="customCursor"></div>
        <div class="custom-cursor-dot" id="customCursorDot"></div>

        <div class="admin-wrapper">
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <div class="sidebar-logo">
                    <h2>Visión de Futuro</h2>
                    <span>Servicios Integrales</span>
                </div>
                <nav class="sidebar-menu">
                    <ul>
                        <li>
                            <a href="index.php" class="' . ($current_page === 'index.php' ? 'active' : '') . '">
                                <i data-lucide="layout-dashboard"></i>
                                <span>Resumen</span>
                            </a>
                        </li>
                        <li>
                            <a href="settings.php" class="' . ($current_page === 'settings.php' ? 'active' : '') . '">
                                <i data-lucide="sliders"></i>
                                <span>Textos y Secciones</span>
                            </a>
                        </li>
                        <li>
                            <a href="gallery.php" class="' . ($current_page === 'gallery.php' ? 'active' : '') . '">
                                <i data-lucide="image"></i>
                                <span>Galería de Fotos</span>
                            </a>
                        </li>
                        <li>
                            <a href="charity_works.php" class="' . ($current_page === 'charity_works.php' ? 'active' : '') . '">
                                <i data-lucide="heart-handshake"></i>
                                <span>Obras de Caridad</span>
                            </a>
                        </li>
                        <li>
                            <a href="testimonials.php" class="' . ($current_page === 'testimonials.php' ? 'active' : '') . '">
                                <i data-lucide="message-circle"></i>
                                <span>Testimonios</span>';
                                if ($pending_testimonials > 0) {
                                    echo '<span class="unread-badge">' . $pending_testimonials . '</span>';
                                }
                            echo '</a>
                        </li>
                        <li>
                            <a href="services.php" class="' . ($current_page === 'services.php' ? 'active' : '') . '">
                                <i data-lucide="utensils"></i>
                                <span>Servicios</span>
                            </a>
                        </li>
                        <li>
                            <a href="benefits.php" class="' . ($current_page === 'benefits.php' ? 'active' : '') . '">
                                <i data-lucide="award"></i>
                                <span>¿Por qué elegirnos?</span>
                            </a>
                        </li>
                        <li>
                            <a href="values.php" class="' . ($current_page === 'values.php' ? 'active' : '') . '">
                                <i data-lucide="heart"></i>
                                <span>Valores</span>
                            </a>
                        </li>
                        <li>
                            <a href="messages.php" class="' . ($current_page === 'messages.php' ? 'active' : '') . '">
                                <i data-lucide="mail"></i>
                                <span>Bandeja de Entrada</span>';
                                if ($unread_count > 0) {
                                    echo '<span class="unread-badge">' . $unread_count . '</span>';
                                }
                            echo '</a>
                        </li>
                    </ul>
                </nav>
                <div class="sidebar-footer">
                    <div class="user-info">
                        <div class="user-avatar">' . strtoupper(substr($_SESSION['admin_username'], 0, 1)) . '</div>
                        <div class="user-details">
                            <p class="username">' . htmlspecialchars($_SESSION['admin_username']) . '</p>
                            <p class="role">Administrador</p>
                        </div>
                    </div>
                    <a href="logout.php" class="btn-logout" title="Cerrar Sesión">
                        <i data-lucide="log-out"></i>
                        <span>Cerrar Sesión</span>
                    </a>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="admin-main">
                <!-- Topbar -->
                <header class="admin-topbar">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i data-lucide="menu"></i>
                    </button>
                    <div class="topbar-title">
                        <h1>' . htmlspecialchars($title) . '</h1>
                    </div>
                    <div class="topbar-actions">
                        <a href="../index.php" target="_blank" class="btn-view-site">
                            <i data-lucide="external-link"></i>
                            <span>Ver Sitio Web</span>
                        </a>
                    </div>
                </header>
                
                <!-- Content Body -->
                <div class="admin-content">';
}

function render_admin_footer() {
    echo '      </div>
            </main>
        </div>
        
        <script>
            // Inicializar iconos de Lucide
            lucide.createIcons();
            
            // Toggle Sidebar en Móviles
            const sidebarToggle = document.getElementById("sidebarToggle");
            const sidebar = document.querySelector(".admin-sidebar");
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener("click", () => {
                    sidebar.classList.toggle("mobile-open");
                });
            }

            // --- Cursor Trail Personalizado ---
            const cursor = document.getElementById("customCursor");
            const cursorDot = document.getElementById("customCursorDot");

            if (cursor && cursorDot && window.matchMedia("(pointer: fine)").matches) {
                let mouseX = 0, mouseY = 0;
                let cursorX = 0, cursorY = 0;

                document.addEventListener("mousemove", (e) => {
                    mouseX = e.clientX;
                    mouseY = e.clientY;
                    
                    // Posicionamiento instantáneo de la yema central
                    cursorDot.style.left = mouseX + "px";
                    cursorDot.style.top = mouseY + "px";
                });

                function animateCursor() {
                    // LERP (Linear Interpolation) para el aro externo
                    let dx = mouseX - cursorX;
                    let dy = mouseY - cursorY;
                    cursorX += dx * 0.15;
                    cursorY += dy * 0.15;

                    cursor.style.left = cursorX + "px";
                    cursor.style.top = cursorY + "px";

                    requestAnimationFrame(animateCursor);
                }
                animateCursor();

                // Efecto hover expandido sobre enlaces e inputs
                const hoverables = document.querySelectorAll("a, button, input, select, textarea, .btn-icon, .stat-card, tr");
                hoverables.forEach(el => {
                    el.addEventListener("mouseenter", () => {
                        cursor.classList.add("cursor-hover");
                        cursorDot.classList.add("cursor-dot-hover");
                    });
                    el.addEventListener("mouseleave", () => {
                        cursor.classList.remove("cursor-hover");
                        cursorDot.classList.remove("cursor-dot-hover");
                    });
                });
            } else {
                if (cursor) cursor.style.display = "none";
                if (cursorDot) cursorDot.style.display = "none";
            }
        </script>
    </body>
    </html>';
}

/**
 * Procesa la subida de una imagen y devuelve su ruta relativa.
 * Opcionalmente borra el archivo anterior si era una subida local previa.
 */
function upload_admin_image($file_input_name, $existing_path = '') {
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] === UPLOAD_ERR_NO_FILE) {
        return $existing_path;
    }

    $file = $_FILES[$file_input_name];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Error al subir archivo (Código: " . $file['error'] . ")");
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("El archivo supera el límite de tamaño permitido de 5MB.");
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_mime = mime_content_type($file['tmp_name']);
    if (!in_array($file_mime, $allowed_types)) {
        throw new Exception("Tipo de archivo no permitido. Solo se aceptan imágenes JPG, PNG, GIF y WEBP.");
    }
    
    $upload_dir = __DIR__ . '/../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . $ext;
    $target_path = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // Eliminar el archivo viejo si existía y era local
        if (!empty($existing_path) && str_starts_with($existing_path, 'uploads/') && file_exists(__DIR__ . '/../' . $existing_path)) {
            unlink(__DIR__ . '/../' . $existing_path);
        }
        return 'uploads/' . $filename;
    } else {
        throw new Exception("No se pudo mover el archivo subido al servidor.");
    }
}
?>
