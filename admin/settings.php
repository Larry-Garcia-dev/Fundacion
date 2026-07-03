<?php
require_once __DIR__ . '/admin_layout.php';

$message = '';
$error = '';

// Migración automática: Insertar configuraciones nuevas si no existen
$new_keys = [
    'theme_color_primary' => '#9b98d5',
    'theme_color_secondary' => '#86d2f1',
    'theme_color_dark' => '#1e293b',
    'hero_bg_image' => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=1920&q=80',
    'about_img_url' => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=800&q=80',
    'logo_path' => 'logo1.png',
    'hero_logo_path' => 'logo1.png',
    'contact_logo_path' => 'logo1.png',
    'footer_logo_path' => 'logo1.png',
    'testimonials_enabled' => '1',
    'logo_size' => '72',
    'logo_offset_x' => '0',
    'logo_offset_y' => '0'
];

try {
    foreach ($new_keys as $k => $v) {
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM `settings` WHERE `key_name` = ?");
        $check_stmt->execute([$k]);
        if ($check_stmt->fetchColumn() == 0) {
            $ins_stmt = $pdo->prepare("INSERT INTO `settings` (key_name, value_text) VALUES (?, ?)");
            $ins_stmt->execute([$k, $v]);
        }
    }
} catch (PDOException $e) {
    $error = 'Error de migración: ' . $e->getMessage();
}

// Obtener configuraciones actuales
$settings = [];
try {
    $stmt = $pdo->query("SELECT * FROM `settings`");
    while ($row = $stmt->fetch()) {
        $settings[$row['key_name']] = $row['value_text'];
    }
} catch (PDOException $e) {
    $error = 'Error al cargar configuraciones: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    try {
        $pdo->beginTransaction();
        
        // Procesar subida de logotipo
        $logo_path = $settings['logo_path'] ?? 'logo1.png';
        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $logo_path = upload_admin_image('logo_file', $logo_path);
                $_POST['settings']['logo_path'] = $logo_path;
            } catch (Exception $ex) {
                $error = $ex->getMessage();
            }
        }

        // Procesar subida de logo del Hero
        $hero_logo_path = $settings['hero_logo_path'] ?? 'logo1.png';
        if (isset($_FILES['hero_logo_file']) && $_FILES['hero_logo_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $hero_logo_path = upload_admin_image('hero_logo_file', $hero_logo_path);
                $_POST['settings']['hero_logo_path'] = $hero_logo_path;
            } catch (Exception $ex) {
                $error = $ex->getMessage();
            }
        }

        // Procesar subida de logo de Contacto
        $contact_logo_path = $settings['contact_logo_path'] ?? 'logo1.png';
        if (isset($_FILES['contact_logo_file']) && $_FILES['contact_logo_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $contact_logo_path = upload_admin_image('contact_logo_file', $contact_logo_path);
                $_POST['settings']['contact_logo_path'] = $contact_logo_path;
            } catch (Exception $ex) {
                $error = $ex->getMessage();
            }
        }

        // Procesar subida de fondo Hero
        $hero_bg_path = $settings['hero_bg_image'] ?? '';
        if (isset($_FILES['hero_bg_image_file']) && $_FILES['hero_bg_image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $hero_bg_path = upload_admin_image('hero_bg_image_file', $hero_bg_path);
                $_POST['settings']['hero_bg_image'] = $hero_bg_path;
            } catch (Exception $ex) {
                $error = $ex->getMessage();
            }
        }
        
        // Procesar subida de Nosotros
        $about_img_path = $settings['about_img_url'] ?? '';
        if (isset($_FILES['about_img_url_file']) && $_FILES['about_img_url_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $about_img_path = upload_admin_image('about_img_url_file', $about_img_path);
                $_POST['settings']['about_img_url'] = $about_img_path;
            } catch (Exception $ex) {
                $error = $ex->getMessage();
            }
        }
        
        if (empty($error)) {
            $stmt_up = $pdo->prepare("UPDATE `settings` SET `value_text` = ? WHERE `key_name` = ?");

            // Los checkboxes no se envían cuando están desmarcados, así que los forzamos a 0
            if (!isset($_POST['settings']['testimonials_enabled'])) {
                $_POST['settings']['testimonials_enabled'] = '0';
            }

            foreach ($_POST['settings'] as $key => $value) {
                $stmt_up->execute([$value, $key]);
            }
            
            $pdo->commit();
            $message = 'Configuraciones y apariencia del sitio actualizadas correctamente.';
            
            // Recargar configuraciones actualizadas
            $stmt = $pdo->query("SELECT * FROM `settings`");
            $settings = [];
            while ($row = $stmt->fetch()) {
                $settings[$row['key_name']] = $row['value_text'];
            }
        } else {
            $pdo->rollBack();
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = 'Error al guardar configuraciones: ' . $e->getMessage();
    }
}

// Logos prediseñados disponibles en la carpeta logos/
$preset_logos = [
    'logos/VisiónLogo_Blanco.png' => 'Blanco',
    'logos/VisiónLogo_Full.png' => 'Full Color',
    'logos/VisiónLogo_Morado.png' => 'Morado',
    'logos/VisiónLogo_Negro.png' => 'Negro',
];

// Handler: selección directa de logo prediseñado vía GET (sin JS, sin form)
if (isset($_GET['select_logo']) && isset($_GET['logo_value'])) {
    $allowed_keys = ['logo_path', 'hero_logo_path', 'contact_logo_path', 'footer_logo_path'];
    $key = $_GET['select_logo'];
    $val = $_GET['logo_value'];
    if (in_array($key, $allowed_keys) && array_key_exists($val, $preset_logos)) {
        try {
            $stmt_set = $pdo->prepare("UPDATE `settings` SET `value_text` = ? WHERE `key_name` = ?");
            $stmt_set->execute([$val, $key]);
            header('Location: settings.php?saved=1');
            exit;
        } catch (PDOException $e) {
            $error = 'Error al seleccionar logo: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['saved'])) {
    $message = 'Logo actualizado correctamente.';
}

function render_logo_picker($setting_key, $preset_logos, $current_value) {
    $html = '<div style="margin-bottom: 20px;">';
    $html .= '<label style="font-weight:600; font-size:13px; margin-bottom:8px; display:block;"><i data-lucide="image" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Seleccionar un logo prediseñado</label>';
    $html .= '<div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 8px;">';
    foreach ($preset_logos as $path => $label) {
        $is_selected = ($current_value === $path);
        $border = $is_selected ? 'border-color: var(--primary); box-shadow: 0 0 0 2px var(--primary);' : '';
        $url = 'settings.php?select_logo=' . urlencode($setting_key) . '&logo_value=' . urlencode($path);
        $html .= '<a href="' . htmlspecialchars($url) . '" style="display:flex; flex-direction:column; align-items:center; gap:6px; text-decoration:none; padding:10px; border-radius:10px; border:2px solid var(--border); transition: all 0.2s; ' . $border . '" onmouseover="this.style.borderColor=\'var(--primary)\'" onmouseout="this.style.borderColor=\'var(--border)\'">';
        $html .= '<img src="../' . htmlspecialchars($path) . '" alt="' . htmlspecialchars($label) . '" style="height:50px; max-width:120px; object-fit:contain; background: ' . ($label === 'Blanco' ? '#e2e8f0' : 'white') . '; border-radius:6px; padding:4px;">';
        $html .= '<span style="font-size:11px; font-weight:600; color:var(--text-dark);">' . htmlspecialchars($label) . '</span>';
        $html .= '</a>';
    }
    $html .= '</div>';
    $html .= '<span class="form-help" style="margin-top:6px;">Haz clic en un logo para seleccionarlo inmediatamente.</span>';
    $html .= '</div>';
    return $html;
}

render_admin_header('Configuración de Textos y Apariencia');
?>

<?php if ($message): ?>
    <div class="alert alert-success">
        <i data-lucide="check-circle"></i>
        <span><?php echo htmlspecialchars($message); ?></span>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i data-lucide="alert-circle"></i>
        <span><?php echo htmlspecialchars($error); ?></span>
    </div>
<?php endif; ?>

<div class="settings-tabs-container" style="display: flex; flex-direction: column; gap: 20px;">
    <!-- Botones de Pestañas (Reorganizadas por UX) -->
    <div class="settings-tabs" style="display: flex; gap: 10px; border-bottom: 2px solid var(--border-light); padding-bottom: 10px; overflow-x: auto;">
        <button class="btn btn-outline tab-btn active" onclick="switchTab(event, 'tab-branding')" style="border-color: var(--primary); color: var(--primary);">
            <i data-lucide="palette"></i> Logo y Colores
        </button>
        <button class="btn btn-outline tab-btn" onclick="switchTab(event, 'tab-hero')">
            <i data-lucide="airplay"></i> Hero (Inicio)
        </button>
        <button class="btn btn-outline tab-btn" onclick="switchTab(event, 'tab-about')">
            <i data-lucide="users"></i> ¿Quiénes Somos?
        </button>
        <button class="btn btn-outline tab-btn" onclick="switchTab(event, 'tab-mission')">
            <i data-lucide="compass"></i> Misión y Visión
        </button>
        <button class="btn btn-outline tab-btn" onclick="switchTab(event, 'tab-cta')">
            <i data-lucide="help-circle"></i> ¿Trabajemos Juntos?
        </button>
        <button class="btn btn-outline tab-btn" onclick="switchTab(event, 'tab-footer')">
            <i data-lucide="info"></i> Contacto y Footer
        </button>
    </div>

    <!-- Contenido de Pestañas -->
    <form action="settings.php" method="POST" class="admin-card" enctype="multipart/form-data">
        <input type="hidden" name="update_settings" value="1">
        
        <!-- Pestaña 1: LOGO Y COLORES (NUEVO ORDEN PRIORITARIO) -->
        <div id="tab-branding" class="tab-content active-content">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Logo del Navbar / Header (Barra de Navegación)</h3>
            <?php echo render_logo_picker('logo_path', $preset_logos, $settings['logo_path'] ?? 'logo1.png'); ?>
            <div class="form-grid">
                <!-- Logo File Upload / URL -->
                <div class="form-group full-width">
                    <label>Dirección actual del Logo</label>
                    <input type="text" id="logo_path_display" value="<?php echo htmlspecialchars($settings['logo_path'] ?? 'logo.jpeg'); ?>" readonly style="opacity:0.7; cursor:default;">
                    <span class="form-help">Para cambiar el logo, selecciona uno de los prediseñados arriba o sube un archivo desde tu PC abajo.</span>
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 15px;">
                        <label for="logo_file" class="btn btn-outline" style="padding: 8px 14px; font-size: 12px; cursor: pointer; display: inline-flex; gap: 6px; margin-bottom: 0;">
                            <i data-lucide="upload" style="width: 14px; height: 14px;"></i> Subir Logo desde PC
                        </label>
                        <input type="file" id="logo_file" name="logo_file" accept="image/*" style="display: none;" onchange="updateFileName(this, 'logo_file_name')">
                        <span id="logo_file_name" style="font-size: 12.5px; color: var(--secondary); font-weight: 600;">
                            <?php echo (isset($settings['logo_path']) && str_starts_with($settings['logo_path'], 'uploads/')) ? 'Archivo activo: ' . basename($settings['logo_path']) : 'Ningún archivo subido'; ?>
                        </span>
                    </div>
                    <?php if (!empty($settings['logo_path'])): ?>
                        <div style="margin-top: 12px;">
                            <?php
                            $main_prev = $settings['logo_path'];
                            if (str_starts_with($main_prev, 'uploads/')) {
                                $main_prev = '../' . $main_prev;
                            } elseif (!str_starts_with($main_prev, 'http')) {
                                $main_prev = '../' . $main_prev;
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($main_prev); ?>" alt="Vista previa logo" style="max-height: 80px; border: 1px solid var(--border); border-radius: 6px; padding: 4px; background: white;">
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Logo Size (Height) -->
                <div class="form-group">
                    <label for="logo_size">Altura del Logo (en píxeles)</label>
                    <input type="number" id="logo_size" name="settings[logo_size]" value="<?php echo htmlspecialchars($settings['logo_size'] ?? '72'); ?>" min="20" max="250" required>
                    <span class="form-help">Altura visual en el menú superior (Predeterminado: 72px).</span>
                </div>

                <!-- Logo Offset X -->
                <div class="form-group">
                    <label for="logo_offset_x">Desplazamiento Horizontal (X en px)</label>
                    <input type="number" id="logo_offset_x" name="settings[logo_offset_x]" value="<?php echo htmlspecialchars($settings['logo_offset_x'] ?? '0'); ?>" required>
                    <span class="form-help">Mueve el logo lateralmente (Números negativos: izquierda, positivos: derecha).</span>
                </div>

                <!-- Logo Offset Y -->
                <div class="form-group">
                    <label for="logo_offset_y">Desplazamiento Vertical (Y en px)</label>
                    <input type="number" id="logo_offset_y" name="settings[logo_offset_y]" value="<?php echo htmlspecialchars($settings['logo_offset_y'] ?? '0'); ?>" required>
                    <span class="form-help">Mueve el logo verticalmente (Números negativos: arriba, positivos: abajo).</span>
                </div>
            </div>

            <hr style="margin: 30px 0; border: none; border-bottom: 1px solid var(--border-light);">

            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Logo del Hero (Sección Principal)</h3>
            <?php echo render_logo_picker('hero_logo_path', $preset_logos, $settings['hero_logo_path'] ?? 'logo1.png'); ?>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Imagen actual del Logo del Hero</label>
                    <input type="text" id="hero_logo_path_display" value="<?php echo htmlspecialchars($settings['hero_logo_path'] ?? 'logo1.png'); ?>" readonly style="opacity:0.7; cursor:default;">
                    <span class="form-help">Logo grande que aparece en la parte derecha del Hero. Selecciona un prediseñado o sube un archivo.</span>
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 15px;">
                        <label for="hero_logo_file" class="btn btn-outline" style="padding: 8px 14px; font-size: 12px; cursor: pointer; display: inline-flex; gap: 6px; margin-bottom: 0;">
                            <i data-lucide="upload" style="width: 14px; height: 14px;"></i> Subir Logo del Hero
                        </label>
                        <input type="file" id="hero_logo_file" name="hero_logo_file" accept="image/*" style="display: none;" onchange="updateFileName(this, 'hero_logo_file_name')">
                        <span id="hero_logo_file_name" style="font-size: 12.5px; color: var(--secondary); font-weight: 600;">
                            <?php echo (isset($settings['hero_logo_path']) && str_starts_with($settings['hero_logo_path'], 'uploads/')) ? 'Archivo activo: ' . basename($settings['hero_logo_path']) : 'Ningún archivo subido'; ?>
                        </span>
                    </div>
                    <?php if (!empty($settings['hero_logo_path'])): ?>
                        <div style="margin-top: 12px;">
                            <?php
                            $hero_prev = $settings['hero_logo_path'];
                            if (str_starts_with($hero_prev, 'uploads/')) {
                                $hero_prev = '../' . $hero_prev;
                            } elseif (!str_starts_with($hero_prev, 'http')) {
                                $hero_prev = '../' . $hero_prev;
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($hero_prev); ?>" alt="Vista previa logo Hero" style="max-height: 120px; border: 1px solid var(--border); border-radius: 6px; padding: 4px; background: white;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <hr style="margin: 30px 0; border: none; border-bottom: 1px solid var(--border-light);">

            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Logo de la Sección Contacto</h3>
            <?php echo render_logo_picker('contact_logo_path', $preset_logos, $settings['contact_logo_path'] ?? 'logo1.png'); ?>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Imagen actual del Logo de Contacto</label>
                    <input type="text" id="contact_logo_path_display" value="<?php echo htmlspecialchars($settings['contact_logo_path'] ?? 'logo1.png'); ?>" readonly style="opacity:0.7; cursor:default;">
                    <span class="form-help">Logo grande que aparece en la sección "¿Trabajemos juntos?". Selecciona un prediseñado o sube un archivo.</span>
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 15px;">
                        <label for="contact_logo_file" class="btn btn-outline" style="padding: 8px 14px; font-size: 12px; cursor: pointer; display: inline-flex; gap: 6px; margin-bottom: 0;">
                            <i data-lucide="upload" style="width: 14px; height: 14px;"></i> Subir Logo de Contacto
                        </label>
                        <input type="file" id="contact_logo_file" name="contact_logo_file" accept="image/*" style="display: none;" onchange="updateFileName(this, 'contact_logo_file_name')">
                        <span id="contact_logo_file_name" style="font-size: 12.5px; color: var(--secondary); font-weight: 600;">
                            <?php echo (isset($settings['contact_logo_path']) && str_starts_with($settings['contact_logo_path'], 'uploads/')) ? 'Archivo activo: ' . basename($settings['contact_logo_path']) : 'Ningún archivo subido'; ?>
                        </span>
                    </div>
                    <?php if (!empty($settings['contact_logo_path'])): ?>
                        <div style="margin-top: 12px;">
                            <?php
                            $contact_prev = $settings['contact_logo_path'];
                            if (str_starts_with($contact_prev, 'uploads/')) {
                                $contact_prev = '../' . $contact_prev;
                            } elseif (!str_starts_with($contact_prev, 'http')) {
                                $contact_prev = '../' . $contact_prev;
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($contact_prev); ?>" alt="Vista previa logo Contacto" style="max-height: 120px; border: 1px solid var(--border); border-radius: 6px; padding: 4px; background: white;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <hr style="margin: 30px 0; border: none; border-bottom: 1px solid var(--border-light);">

            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Logo del Footer (Pie de Página)</h3>
            <?php echo render_logo_picker('footer_logo_path', $preset_logos, $settings['footer_logo_path'] ?? 'logo1.png'); ?>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Imagen actual del Logo del Footer</label>
                    <input type="text" id="footer_logo_path_display" value="<?php echo htmlspecialchars($settings['footer_logo_path'] ?? 'logo1.png'); ?>" readonly style="opacity:0.7; cursor:default;">
                    <span class="form-help">Logo que aparece en el pie de página. Selecciona un prediseñado o sube un archivo.</span>
                </div>
            </div>

            <hr style="margin: 30px 0; border: none; border-bottom: 1px solid var(--border-light);">

            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Secciones del Sitio</h3>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label style="display: flex; align-items: center; gap: 14px; cursor: pointer; font-weight: 600; font-size: 14px; color: var(--text-dark);">
                        <div class="toggle-switch">
                            <input type="checkbox" id="testimonials_enabled" name="settings[testimonials_enabled]" value="1" <?php echo !empty($settings['testimonials_enabled']) ? 'checked' : ''; ?>>
                            <span class="toggle-slider"></span>
                        </div>
                        Mostrar sección de Testimonios en la landing page
                    </label>
                    <span class="form-help" style="margin-left: 42px;">Activa o desactiva la visibilidad de la sección de testimonios. Los testimonios enviados seguirán guardándose aunque esté desactivada.</span>
                </div>
            </div>

            <hr style="margin: 30px 0; border: none; border-bottom: 1px solid var(--border-light);">

            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Paleta de Colores del Sitio</h3>
            <div class="form-grid" style="grid-template-columns: repeat(3, 1fr);">
                <div class="form-group">
                    <label for="theme_color_primary">Color Principal (Lila del Logo)</label>
                    <input type="color" id="theme_color_primary" name="settings[theme_color_primary]" value="<?php echo htmlspecialchars($settings['theme_color_primary'] ?? '#9b98d5'); ?>" style="height: 50px; padding: 5px; cursor: pointer;">
                </div>
                <div class="form-group">
                    <label for="theme_color_secondary">Color Secundario (Celeste del Logo)</label>
                    <input type="color" id="theme_color_secondary" name="settings[theme_color_secondary]" value="<?php echo htmlspecialchars($settings['theme_color_secondary'] ?? '#86d2f1'); ?>" style="height: 50px; padding: 5px; cursor: pointer;">
                </div>
                <div class="form-group">
                    <label for="theme_color_dark">Color Oscuro (Fondo / Textos)</label>
                    <input type="color" id="theme_color_dark" name="settings[theme_color_dark]" value="<?php echo htmlspecialchars($settings['theme_color_dark'] ?? '#1e293b'); ?>" style="height: 50px; padding: 5px; cursor: pointer;">
                </div>
            </div>
        </div>
        
        <!-- Pestaña 2: HERO (TEXTOS Y FOTO DE FONDO) -->
        <div id="tab-hero" class="tab-content" style="display: none;">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Sección Hero (Primera Pantalla)</h3>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="hero_title">Título Principal</label>
                    <input type="text" id="hero_title" name="settings[hero_title]" value="<?php echo htmlspecialchars($settings['hero_title'] ?? ''); ?>" required>
                </div>
                <div class="form-group full-width">
                    <label for="hero_subtitle">Subtítulo descriptivo</label>
                    <textarea id="hero_subtitle" name="settings[hero_subtitle]" rows="3" required><?php echo htmlspecialchars($settings['hero_subtitle'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="hero_btn_primary">Texto Botón Principal</label>
                    <input type="text" id="hero_btn_primary" name="settings[hero_btn_primary]" value="<?php echo htmlspecialchars($settings['hero_btn_primary'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="hero_btn_secondary">Texto Botón Secundario</label>
                    <input type="text" id="hero_btn_secondary" name="settings[hero_btn_secondary]" value="<?php echo htmlspecialchars($settings['hero_btn_secondary'] ?? ''); ?>" required>
                </div>
                
                <!-- Imagen de Fondo Hero Integrada Aquí -->
                <div class="form-group full-width" style="margin-top: 10px; padding-top: 20px; border-top: 1px dashed var(--border);">
                    <label for="hero_bg_image"><strong>Imagen de Fondo Grande (Hero Background)</strong></label>
                    <input type="text" id="hero_bg_image" name="settings[hero_bg_image]" value="<?php echo htmlspecialchars($settings['hero_bg_image'] ?? ''); ?>" placeholder="https://ejemplo.com/imagen.jpg">
                    <span class="form-help">Ingresa una dirección URL externa, o sube un archivo local abajo.</span>
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 15px;">
                        <label for="hero_bg_image_file" class="btn btn-outline" style="padding: 8px 14px; font-size: 12px; cursor: pointer; display: inline-flex; gap: 6px; margin-bottom: 0;">
                            <i data-lucide="upload" style="width: 14px; height: 14px;"></i> Subir Imagen desde el PC
                        </label>
                        <input type="file" id="hero_bg_image_file" name="hero_bg_image_file" accept="image/*" style="display: none;" onchange="updateFileName(this, 'hero_file_name')">
                        <span id="hero_file_name" style="font-size: 12.5px; color: var(--secondary); font-weight: 600;">
                            <?php echo (isset($settings['hero_bg_image']) && str_starts_with($settings['hero_bg_image'], 'uploads/')) ? 'Archivo activo: ' . basename($settings['hero_bg_image']) : 'Ningún archivo subido'; ?>
                        </span>
                    </div>
                    <?php if (!empty($settings['hero_bg_image'])): ?>
                        <div style="margin-top: 12px;">
                            <?php
                            $bg_prev = $settings['hero_bg_image'];
                            if (str_starts_with($bg_prev, 'uploads/')) {
                                $bg_prev = '../' . $bg_prev;
                            } elseif (!str_starts_with($bg_prev, 'http')) {
                                $bg_prev = '../' . $bg_prev;
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($bg_prev); ?>" alt="Vista previa fondo Hero" style="max-height: 120px; max-width: 300px; border: 1px solid var(--border); border-radius: 6px; padding: 4px; background: white;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Pestaña 3: QUIÉNES SOMOS (TEXTOS Y FOTO DE SECCIÓN) -->
        <div id="tab-about" class="tab-content" style="display: none;">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Sección ¿Quiénes Somos?</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="about_title">Título de la Sección</label>
                    <input type="text" id="about_title" name="settings[about_title]" value="<?php echo htmlspecialchars($settings['about_title'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="about_subtitle">Subtítulo de la Sección</label>
                    <input type="text" id="about_subtitle" name="settings[about_subtitle]" value="<?php echo htmlspecialchars($settings['about_subtitle'] ?? ''); ?>" required>
                </div>
                <div class="form-group full-width">
                    <label for="about_content_1">Párrafo de Descripción 1</label>
                    <textarea id="about_content_1" name="settings[about_content_1]" rows="3" required><?php echo htmlspecialchars($settings['about_content_1'] ?? ''); ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="about_content_2">Párrafo de Descripción 2</label>
                    <textarea id="about_content_2" name="settings[about_content_2]" rows="3" required><?php echo htmlspecialchars($settings['about_content_2'] ?? ''); ?></textarea>
                </div>

                <!-- Imagen Nosotros Integrada Aquí -->
                <div class="form-group full-width" style="margin-top: 10px; padding-top: 20px; border-top: 1px dashed var(--border);">
                    <label for="about_img_url"><strong>Imagen Descriptiva de la Sección Nosotros</strong></label>
                    <input type="text" id="about_img_url" name="settings[about_img_url]" value="<?php echo htmlspecialchars($settings['about_img_url'] ?? ''); ?>" placeholder="https://ejemplo.com/nosotros.jpg">
                    <span class="form-help">Ingresa una dirección URL externa, o sube un archivo local abajo.</span>
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 15px;">
                        <label for="about_img_url_file" class="btn btn-outline" style="padding: 8px 14px; font-size: 12px; cursor: pointer; display: inline-flex; gap: 6px; margin-bottom: 0;">
                            <i data-lucide="upload" style="width: 14px; height: 14px;"></i> Subir Imagen desde el PC
                        </label>
                        <input type="file" id="about_img_url_file" name="about_img_url_file" accept="image/*" style="display: none;" onchange="updateFileName(this, 'about_file_name')">
                        <span id="about_file_name" style="font-size: 12.5px; color: var(--secondary); font-weight: 600;">
                            <?php echo (isset($settings['about_img_url']) && str_starts_with($settings['about_img_url'], 'uploads/')) ? 'Archivo activo: ' . basename($settings['about_img_url']) : 'Ningún archivo subido'; ?>
                        </span>
                    </div>
                    <?php if (!empty($settings['about_img_url'])): ?>
                        <div style="margin-top: 12px;">
                            <?php
                            $about_prev = $settings['about_img_url'];
                            if (str_starts_with($about_prev, 'uploads/')) {
                                $about_prev = '../' . $about_prev;
                            } elseif (!str_starts_with($about_prev, 'http')) {
                                $about_prev = '../' . $about_prev;
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($about_prev); ?>" alt="Vista previa imagen Nosotros" style="max-height: 120px; border: 1px solid var(--border); border-radius: 6px; padding: 4px; background: white;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <hr style="margin: 30px 0; border: none; border-bottom: 1px solid var(--border-light);">

            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Sección Nuestro Compromiso</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="commitment_title">Título de Compromiso</label>
                    <input type="text" id="commitment_title" name="settings[commitment_title]" value="<?php echo htmlspecialchars($settings['commitment_title'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="commitment_subtitle">Subtítulo de Compromiso</label>
                    <input type="text" id="commitment_subtitle" name="settings[commitment_subtitle]" value="<?php echo htmlspecialchars($settings['commitment_subtitle'] ?? ''); ?>" required>
                </div>
                <div class="form-group full-width">
                    <label for="commitment_content">Contenido del Compromiso</label>
                    <textarea id="commitment_content" name="settings[commitment_content]" rows="3" required><?php echo htmlspecialchars($settings['commitment_content'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- Pestaña Mision/Vision -->
        <div id="tab-mission" class="tab-content" style="display: none;">
            <div class="form-grid">
                <div>
                    <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Misión</h3>
                    <div class="form-group">
                        <label for="mission_title">Título de la Misión</label>
                        <input type="text" id="mission_title" name="settings[mission_title]" value="<?php echo htmlspecialchars($settings['mission_title'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="mission_content">Contenido de la Misión</label>
                        <textarea id="mission_content" name="settings[mission_content]" rows="6" required><?php echo htmlspecialchars($settings['mission_content'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <div>
                    <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Visión</h3>
                    <div class="form-group">
                        <label for="vision_title">Título de la Visión</label>
                        <input type="text" id="vision_title" name="settings[vision_title]" value="<?php echo htmlspecialchars($settings['vision_title'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="vision_content">Contenido de la Visión</label>
                        <textarea id="vision_content" name="settings[vision_content]" rows="6" required><?php echo htmlspecialchars($settings['vision_content'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pestaña ¿Trabajemos juntos? -->
        <div id="tab-cta" class="tab-content" style="display: none;">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Sección de Llamado a la Acción</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="together_title">Título Principal</label>
                    <input type="text" id="together_title" name="settings[together_title]" value="<?php echo htmlspecialchars($settings['together_title'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="together_btn">Texto del Botón</label>
                    <input type="text" id="together_btn" name="settings[together_btn]" value="<?php echo htmlspecialchars($settings['together_btn'] ?? ''); ?>" required>
                </div>
                <div class="form-group full-width">
                    <label for="together_content">Contenido descriptivo</label>
                    <textarea id="together_content" name="settings[together_content]" rows="3" required><?php echo htmlspecialchars($settings['together_content'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- Pestaña Footer / Contacto -->
        <div id="tab-footer" class="tab-content" style="display: none;">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Datos de Contacto (Footer)</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="footer_address">Dirección</label>
                    <input type="text" id="footer_address" name="settings[footer_address]" value="<?php echo htmlspecialchars($settings['footer_address'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="footer_phone">Teléfono de Contacto</label>
                    <input type="text" id="footer_phone" name="settings[footer_phone]" value="<?php echo htmlspecialchars($settings['footer_phone'] ?? ''); ?>" required>
                </div>
                <div class="form-group full-width">
                    <label for="footer_email">Correo Electrónico de Contacto</label>
                    <input type="email" id="footer_email" name="settings[footer_email]" value="<?php echo htmlspecialchars($settings['footer_email'] ?? ''); ?>" required>
                </div>
            </div>

            <hr style="margin: 30px 0; border: none; border-bottom: 1px solid var(--border-light);">

            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; margin-bottom: 20px; color: var(--primary);">Redes Sociales</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="footer_facebook">Enlace Facebook</label>
                    <input type="text" id="footer_facebook" name="settings[footer_facebook]" value="<?php echo htmlspecialchars($settings['footer_facebook'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="footer_instagram">Enlace Instagram</label>
                    <input type="text" id="footer_instagram" name="settings[footer_instagram]" value="<?php echo htmlspecialchars($settings['footer_instagram'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="footer_twitter">Enlace Twitter</label>
                    <input type="text" id="footer_twitter" name="settings[footer_twitter]" value="<?php echo htmlspecialchars($settings['footer_twitter'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="footer_linkedin">Enlace LinkedIn</label>
                    <input type="text" id="footer_linkedin" name="settings[footer_linkedin]" value="<?php echo htmlspecialchars($settings['footer_linkedin'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<script>
    function switchTab(evt, tabId) {
        const contents = document.querySelectorAll('.tab-content');
        contents.forEach(content => {
            content.style.display = 'none';
        });

        const buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(btn => {
            btn.classList.remove('active');
        });

        document.getElementById(tabId).style.display = 'block';
        evt.currentTarget.classList.add('active');
    }

    function updateFileName(input, spanId) {
        const span = document.getElementById(spanId);
        if (input.files && input.files.length > 0) {
            span.textContent = "Seleccionado: " + input.files[0].name;
            span.style.color = "var(--secondary)";
        } else {
            span.textContent = "Ningún archivo seleccionado";
            span.style.color = "var(--text-muted)";
        }
    }
</script>

<?php
render_admin_footer();
?>
